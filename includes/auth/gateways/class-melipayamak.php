<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-sms-gateway.php';

class Serene_Gateway_Melipayamak implements Serene_SMS_Gateway_Interface {
    private $username;
    private $password;

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function send_otp($phone, $code, $pattern = '') {
        $url = 'https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber';
        
        $response = wp_remote_post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 15,
            'body'    => wp_json_encode([
                'username' => $this->username,
                'password' => $this->password,
                'text'     => $code,
                'to'       => $phone,
                'bodyId'   => (int) $pattern,
            ])
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($body['RetStatus']) && $body['RetStatus'] == 1) {
            return true;
        }

        return new WP_Error('melipayamak_error', isset($body['StrRetStatus']) ? $body['StrRetStatus'] : 'خطا در ارسال ملی پیامک');
    }

    public function send_custom_sms($phone, $message) {
        return true;
    }

    public function send_voice_call($phone, $code) {
        return new WP_Error('not_supported', 'تماس صوتی برای ملی پیامک در دسترس نیست.');
    }
}
