<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Gateway_Bale {
    private $bot_token;

    public function __construct($bot_token) {
        $this->bot_token = $bot_token;
    }

    public function send_otp($chat_id, $code) {
        $url = sprintf('https://tapi.bale.ai/bot%s/sendMessage', $this->bot_token);
        $text = sprintf("🔐 کد ورود به حساب کاربری: %s
اعتبار: ۲ دقیقه", $code);

        $response = wp_remote_post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => wp_json_encode([
                'chat_id' => $chat_id,
                'text'    => $text,
            ])
        ]);

        if (is_wp_error($response)) {
            return $response;
        }
        return true;
    }
}
