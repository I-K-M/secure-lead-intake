<?php

class LeadValidator {

    public static function validate($data, $config) {

        if (!$data['email'] || !$data['first_name'] || !$data['phone']) {
            return 'Missing required fields';
        }

        if (!is_email($data['email'])) {
            return 'Invalid email';
        }

        if ($config['requires_message'] && !$data['message']) {
            return 'Message required';
        }

        return null;
    }
}