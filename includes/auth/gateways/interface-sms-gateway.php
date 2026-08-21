<?php
if (!defined('ABSPATH')) {
    exit;
}

interface Serene_SMS_Gateway_Interface {
    public function send_otp($phone, $code, $pattern = '');
    public function send_custom_sms($phone, $message);
    public function send_voice_call($phone, $code);
}
