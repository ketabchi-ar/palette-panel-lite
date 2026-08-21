<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Google_OAuth {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function verify_id_token($id_token) {
        $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($id_token);
        $response = wp_remote_get($url, ['timeout' => 15]);

        if (is_wp_error($response)) {
            return $response;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($data['email']) || empty($data['email_verified']) || $data['email_verified'] !== 'true') {
            return new WP_Error('invalid_token', 'اعتبارسنجی حساب گوگل ناموفق بود.');
        }

        return $data;
    }
}
