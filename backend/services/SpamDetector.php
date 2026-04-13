<?php

class SpamDetector {

    public static function analyze($data, $userAgent) {

        $score = 0;

        if ($data['honeypot_1'] || $data['honeypot_2']) {
            return ['blocked' => true];
        }

        if (!$userAgent) {
            return ['blocked' => true];
        }

        if ($data['loaded_at']) {
            $elapsed = (microtime(true) * 1000) - $data['loaded_at'];
            if ($elapsed < 2500) $score += 2;
        }

        if ($data['message'] && preg_match('/(crypto|seo|casino)/i', $data['message'])) {
            $score += 3;
        }

        return [
            'blocked' => $score >= 4,
            'score' => $score
        ];
    }
}