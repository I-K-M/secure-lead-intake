<?php

class FormRegistry {

    public static function get($key) {
        $forms = self::all();
        return $forms[$key] ?? null;
    }

    private static function all() {
        return [
            'financial_consultation' => [
                'tag' => 'Financial Consultation',
                'subject' => 'New Financial Lead',
                'recipients' => ['sales@example.com'],
                'requires_message' => true,
            ],
            'property_inquiry' => [
                'tag' => 'Property Inquiry',
                'subject' => 'New Property Lead',
                'recipients' => ['sales@example.com'],
                'requires_message' => false,
            ],
        ];
    }
}