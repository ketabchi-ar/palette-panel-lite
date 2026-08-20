<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-sms-gateway.php';

class Serene_Gateway_Kavenegar implements Serene_SMS_Gateway_Interface {
    private $api_key;
    private $sender;

    public function __construct($api_key, $sender = '') {
        $this->api_key = $api_key;
        $this->sender  = $sender;
    }

    public function send_otp($phone, $code, $pattern = '') {
        $pattern = $pattern ?: 'serene-otp';
        $url = sprintf('https://api.kavenegar.com/v1/%s/verify/lookup.json', $this->api_key);
        
        $response = wp_remote_post($url, [
            'timeout' => 15,
            'body'    => [
                'receptor' => $phone,
                'token'    => $code,
                'template' => $pattern,
            ]
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($body['return']['status']) && $body['return']['status'] == 200) {
            return true;
        }

        return new WP_Error('kavenegar_error', isset($body['return']['message']) ? $body['return']['message'] : 'خطا در ارسال کاوه نگار');
    }

    public function send_custom_sms($phone, $message) {
        $url = sprintf('https://api.kavenegar.com/v1/%s/sms/send.json', $this->api_key);
        $response = wp_remote_post($url, [
            'timeout' => 15,
            'body'    => [
                'receptor' => $phone,
                'sender'   => $this->sender,
                'message'  => $message,
            ]
        ]);

        if (is_wp_error($response)) {
            return $response;
        }
        return true;
    }

    public function send_voice_call($phone, $code) {
        $options = get_option('serene_panel_options', []);
        $tpl = !empty($options['voice_tts_text']) ? $options['voice_tts_text'] : 'کد تایید شما: %s';
        $spaced_code = implode(' ', str_split($code));
        $msg = sprintf($tpl, $spaced_code);

        $url = sprintf('https://api.kavenegar.com/v1/%s/call/maketts.json', $this->api_key);
        $response = wp_remote_post($url, [
            'timeout' => 15,
            'body'    => [
                'receptor' => $phone,
                'message'  => $msg,
            ]
        ]);

        if (is_wp_error($response)) {
            return $response;
        }
        return true;
    }
}
