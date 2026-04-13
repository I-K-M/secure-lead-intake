<?php

class EmailNotifier {

    public function send($data, $config, $ip, $userAgent, $spam) {

        $message = "
New Lead

Name: {$data['first_name']} {$data['last_name']}
Email: {$data['email']}
Phone: {$data['phone']}

Message:
{$data['message']}

IP: {$ip}
UA: {$userAgent}
Spam Score: {$spam['score']}
";

        wp_mail(
            $config['recipients'],
            $config['subject'],
            $message
        );
    }
}