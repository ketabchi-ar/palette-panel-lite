<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_SMS_Logger {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function log($phone, $provider, $message, $pattern_code = '', $status = 'sent', $response = '', $ip = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_sms_logs';

        $wpdb->insert($table, [
            'phone'        => sanitize_text_field($phone),
            'provider'     => sanitize_text_field($provider),
            'message'      => sanitize_textarea_field($message),
            'pattern_code' => sanitize_text_field($pattern_code),
            'status'       => sanitize_text_field($status),
            'response'     => is_array($response) || is_object($response) ? wp_json_encode($response) : (string) $response,
            'ip_address'   => $ip ?: Serene_Panel_Rate_Limiter::get_client_ip(),
            'created_at'   => current_time('mysql')
        ]);

        return $wpdb->insert_id;
    }

    public static function get_recent_logs($limit = 50, $offset = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_sms_logs';
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table ORDER BY id DESC LIMIT %d OFFSET %d",
            $limit, $offset
        ));
    }
}
