<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_License_Manager {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function get_user_licenses($user_id) {
        return [
            [
                'key'         => 'SERENE-' . strtoupper(wp_generate_password(16, false, false)),
                'product'     => 'قالب و پنل هوشمند ووکامرس',
                'domain'      => parse_url(home_url(), PHP_URL_HOST),
                'status'      => 'active',
                'expiry_date' => '۱۴۰۵/۰۵/۲۰',
            ]
        ];
    }
}
