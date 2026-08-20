<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_OTP_Service {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function generate_code($length = 5) {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return (string) wp_rand($min, $max);
    }

    public static function send_otp($recipient, $type = 'sms', $method = 'text') {
        $options = get_option('serene_panel_options', []);
        $length  = isset($options['otp_length']) ? (int) $options['otp_length'] : 5;
        $expiry  = isset($options['otp_expiry']) ? (int) $options['otp_expiry'] : 120;
        
        $code = self::generate_code($length);
        $hash = wp_hash_password($code);

        // Store in transient
        $transient_key = 'serene_otp_' . md5($recipient);
        set_transient($transient_key, [
            'hash'     => $hash,
            'attempts' => 0,
            'created'  => time(),
        ], $expiry);

        if ($type === 'email') {
            require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-email-otp.php';
            $result = Serene_Gateway_Email::send_otp($recipient, $code);
            Serene_Panel_SMS_Logger::log($recipient, 'email', 'کد ارسال شد', '', $result ? 'sent' : 'failed');
            return $result;
        }

        if ($type === 'bale') {
            require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-bale-bot.php';
            $bot = new Serene_Gateway_Bale(isset($options['bale_token']) ? $options['bale_token'] : '');
            return $bot->send_otp($recipient, $code);
        }

        if ($type === 'telegram') {
            require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-telegram-bot.php';
            $bot = new Serene_Gateway_Telegram(isset($options['telegram_token']) ? $options['telegram_token'] : '');
            return $bot->send_otp($recipient, $code);
        }

        // SMS or Voice
        $provider_slug = !empty($options['sms_provider']) ? $options['sms_provider'] : 'smsir';

        $gateway = null;
        $pattern = '';

        switch ($provider_slug) {
            case 'smsir':
                require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-smsir.php';
                $api_key = !empty($options['smsir_api_key']) ? trim($options['smsir_api_key']) : ($options['sms_api_key'] ?? '');
                $pattern = !empty($options['smsir_template_id']) ? trim($options['smsir_template_id']) : ($options['sms_otp_pattern'] ?? '');
                $gateway = new Serene_Gateway_SmsIr($api_key);
                break;
            case 'melipayamak':
                require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-melipayamak.php';
                $user = !empty($options['melipayamak_username']) ? trim($options['melipayamak_username']) : ($options['sms_api_key'] ?? '');
                $pass = !empty($options['melipayamak_password']) ? trim($options['melipayamak_password']) : ($options['sms_password'] ?? '');
                $pattern = !empty($options['melipayamak_pattern']) ? trim($options['melipayamak_pattern']) : ($options['sms_otp_pattern'] ?? '');
                $gateway = new Serene_Gateway_Melipayamak($user, $pass);
                break;
            case 'niksms':
                require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-niksms.php';
                $user = !empty($options['niksms_username']) ? trim($options['niksms_username']) : ($options['sms_api_key'] ?? '');
                $pattern = !empty($options['niksms_pattern']) ? trim($options['niksms_pattern']) : ($options['sms_otp_pattern'] ?? '');
                $gateway = new Serene_Gateway_NikSms($user);
                break;
            case 'kavenegar':
            default:
                require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-kavenegar.php';
                $api_key = !empty($options['kavenegar_api_key']) ? trim($options['kavenegar_api_key']) : ($options['sms_api_key'] ?? '');
                $sender  = !empty($options['kavenegar_sender']) ? trim($options['kavenegar_sender']) : ($options['sms_sender'] ?? '');
                $pattern = !empty($options['kavenegar_pattern']) ? trim($options['kavenegar_pattern']) : ($options['sms_otp_pattern'] ?? '');
                $gateway = new Serene_Gateway_Kavenegar($api_key, $sender);
                break;
        }

        if ($method === 'voice') {
            $res = $gateway->send_voice_call($recipient, $code);
        } else {
            $res = $gateway->send_otp($recipient, $code, $pattern);
        }

        $status = is_wp_error($res) ? 'failed' : 'sent';
        $resp_msg = is_wp_error($res) ? $res->get_error_message() : 'OK';
        Serene_Panel_SMS_Logger::log($recipient, $provider_slug, sprintf('ارسال کد اعتبارسنجی: %s', $code), $pattern, $status, $resp_msg);

        return $res;
    }

    public static function verify_code($recipient, $code) {
        $transient_key = 'serene_otp_' . md5($recipient);
        $stored = get_transient($transient_key);

        if (!$stored || !isset($stored['hash'])) {
            return new WP_Error('otp_expired', 'کد تایید منقضی شده یا نامعتبر است. لطفاً مجدداً درخواست کد دهید.');
        }

        if ($stored['attempts'] >= 3) {
            delete_transient($transient_key);
            return new WP_Error('too_many_attempts', 'تعداد تلاش‌های ناموفق بیش از حد مجاز بود. لطفاً کد جدید دریافت کنید.');
        }

        if (wp_check_password($code, $stored['hash'])) {
            delete_transient($transient_key);
            return true;
        }

        // Increment attempt
        $stored['attempts']++;
        set_transient($transient_key, $stored, 120);

        return new WP_Error('invalid_code', 'کد وارد شده نادرست است.');
    }

    public static function send_sms($phone, $message, $pattern = '') {
        $options = get_option('serene_panel_options', []);
        $provider_slug = !empty($options['sms_provider']) ? $options['sms_provider'] : 'smsir';

        switch ($provider_slug) {
            case 'smsir':
                require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-smsir.php';
                $api_key = !empty($options['smsir_api_key']) ? trim($options['smsir_api_key']) : ($options['sms_api_key'] ?? '');
                $gateway = new Serene_Gateway_SmsIr($api_key);
                break;
            case 'melipayamak':
                require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-melipayamak.php';
                $user = !empty($options['melipayamak_username']) ? trim($options['melipayamak_username']) : ($options['sms_api_key'] ?? '');
                $pass = !empty($options['melipayamak_password']) ? trim($options['melipayamak_password']) : ($options['sms_password'] ?? '');
                $gateway = new Serene_Gateway_Melipayamak($user, $pass);
                break;
            case 'niksms':
                require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-niksms.php';
                $user = !empty($options['niksms_username']) ? trim($options['niksms_username']) : ($options['sms_api_key'] ?? '');
                $gateway = new Serene_Gateway_NikSms($user);
                break;
            case 'kavenegar':
            default:
                require_once SERENE_PANEL_PATH . 'includes/auth/gateways/class-kavenegar.php';
                $api_key = !empty($options['kavenegar_api_key']) ? trim($options['kavenegar_api_key']) : ($options['sms_api_key'] ?? '');
                $sender  = !empty($options['kavenegar_sender']) ? trim($options['kavenegar_sender']) : ($options['sms_sender'] ?? '');
                $gateway = new Serene_Gateway_Kavenegar($api_key, $sender);
                break;
        }

        if (method_exists($gateway, 'send_custom_sms')) {
            return $gateway->send_custom_sms($phone, $message);
        }

        return false;
    }
}

