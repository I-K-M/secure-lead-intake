<?php

require_once __DIR__ . '/../Config/FormRegistry.php';
require_once __DIR__ . '/../Validation/LeadValidator.php';
require_once __DIR__ . '/../Spam/SpamDetector.php';
require_once __DIR__ . '/../RateLimit/RateLimiter.php';
require_once __DIR__ . '/../Integrations/MailchimpClient.php';
require_once __DIR__ . '/../Notifications/EmailNotifier.php';
require_once __DIR__ . '/../Support/ClientIp.php';

class LeadController {

    public static function handle($request) {

        $formSource = sanitize_text_field($request->get_param('form_source'));
        $config = FormRegistry::get($formSource);

        if (!$config) {
            return self::error('INVALID_FORM', 'Invalid form source', 400);
        }

        $payload = [
            'email' => sanitize_email($request->get_param('EMAIL')),
            'first_name' => sanitize_text_field($request->get_param('FNAME')),
            'last_name' => sanitize_text_field($request->get_param('LNAME')),
            'phone' => sanitize_text_field($request->get_param('PHONE')),
            'message' => sanitize_textarea_field($request->get_param('MMERGE7')),
            'loaded_at' => (int) $request->get_param('form_loaded_at'),
            'honeypot_1' => $request->get_param('hp_1'),
            'honeypot_2' => $request->get_param('hp_2'),
        ];

        $ip = ClientIp::get();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Rate limit
        if (!RateLimiter::check($ip, $formSource)) {
            return self::error('RATE_LIMIT', 'Too many attempts', 429);
        }

        // Validation
        $validationError = LeadValidator::validate($payload, $config);
        if ($validationError) {
            return self::error('VALIDATION_ERROR', $validationError, 400);
        }

        // Spam detection
        $spamResult = SpamDetector::analyze($payload, $userAgent);

        if ($spamResult['blocked']) {
            return self::error('SPAM_DETECTED', 'Submission flagged as spam', 400);
        }

        // Mailchimp
        $mc = new MailchimpClient();
        $mc->upsert($payload, $config);

        // Email notification
        $notifier = new EmailNotifier();
        $notifier->send($payload, $config, $ip, $userAgent, $spamResult);

        return self::success('Submission received');
    }

    private static function success($message) {
        return new WP_REST_Response([
            'ok' => true,
            'message' => $message
        ], 200);
    }

    private static function error($code, $message, $status) {
        return new WP_REST_Response([
            'ok' => false,
            'code' => $code,
            'message' => $message
        ], $status);
    }
}