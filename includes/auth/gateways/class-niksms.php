<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-sms-gateway.php';

class Serene_Gateway_NikSms implements Serene_SMS_Gateway_Interface {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function send_otp($phone, $code, $pattern = '') {
        $url = 'https://niksms.com/api/v1/otp';
        
        $response = wp_remote_post($url, [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ],
            'timeout' => 15,
            'body'    => wp_json_encode([
                'phone'     => $phone,
                'code'      => $code,
                'patternId' => $pattern,
            ])
        ]);

        if (is_wp_error($response)) {
            return $response;
        }
        return true;
    }

    public function send_custom_sms($phone, $message) {
        return true;
    }

    public function send_voice_call($phone, $code) {
        return new WP_Error('not_supported', 'تماس صوتی پشتیبانی نمی‌شود.');
    }
}
