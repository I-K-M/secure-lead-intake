<?php
/*
Plugin Name: Secure Lead Intake
Description: Configurable lead intake pipeline with anti-spam, validation, CRM sync, and notifications.
Version: 1.0.0
*/

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/src/Http/LeadController.php';

add_action('rest_api_init', function () {
    register_rest_route('leadflow/v1', '/submit', [
        'methods'  => 'POST',
        'callback' => ['LeadController', 'handle'],
        'permission_callback' => '__return_true',
    ]);
});