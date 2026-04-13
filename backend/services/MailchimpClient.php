<?php

class MailchimpClient {

    public function upsert($data, $config) {

        $apiKey = MAILCHIMP_API_KEY ?? '';
        $audience = MAILCHIMP_AUDIENCE_ID ?? '';
        $server = MAILCHIMP_SERVER_PREFIX ?? 'us1';

        if (!$apiKey || !$audience) return;

        $hash = md5(strtolower($data['email']));

        $payload = [
            'email_address' => $data['email'],
            'status' => 'subscribed',
            'merge_fields' => [
                'FNAME' => $data['first_name'],
                'LNAME' => $data['last_name'],
                'PHONE' => $data['phone'],
            ],
            'tags' => [$config['tag']]
        ];

        wp_remote_request(
            "https://{$server}.api.mailchimp.com/3.0/lists/{$audience}/members/{$hash}",
            [
                'method' => 'PUT',
                'headers' => [
                    'Authorization' => 'apikey ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'body' => wp_json_encode($payload)
            ]
        );
    }
}