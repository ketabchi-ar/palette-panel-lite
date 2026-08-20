<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Rate_Limiter {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function get_client_ip() {
        $ip = '127.0.0.1';
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return sanitize_text_field($ip);
    }

    public static function check_ip_banned($ip = null) {
        $ip = $ip ?: self::get_client_ip();
        $banned_ips = get_option('serene_banned_ips', []);
        if (in_array($ip, $banned_ips, true)) {
            return true;
        }
        return false;
    }

    public static function ban_ip($ip = null) {
        $ip = $ip ?: self::get_client_ip();
        $banned_ips = get_option('serene_banned_ips', []);
        if (!in_array($ip, $banned_ips, true)) {
            $banned_ips[] = $ip;
            update_option('serene_banned_ips', $banned_ips);
        }
    }

    public static function check_sms_rate_limit($phone, $ip = null) {
        $ip = $ip ?: self::get_client_ip();

        if (self::check_ip_banned($ip)) {
            return new WP_Error('ip_banned', 'آدرس IP شما به دلیل فعالیت مشکوک مسدود شده است.');
        }

        // Check countdown between consecutive requests (Anti-flood)
        $transient_cooldown = 'serene_cd_' . md5($phone . '_' . $ip);
        if (get_transient($transient_cooldown)) {
            return new WP_Error('flood_wait', 'لطفاً تا اتمام شمارش معکوس منتظر بمانید.');
        }

        // Check hourly limit for IP
        $ip_count_key = 'serene_rl_ip_' . md5($ip);
        $ip_count = (int) get_transient($ip_count_key);
        if ($ip_count >= 15) {
            self::ban_ip($ip);
            return new WP_Error('too_many_requests', 'تعداد درخواست‌های ارسالی از این IP بیش از حد مجاز است.');
        }
        set_transient($ip_count_key, $ip_count + 1, HOUR_IN_SECONDS);

        // Check phone limit
        $phone_count_key = 'serene_rl_ph_' . md5($phone);
        $phone_count = (int) get_transient($phone_count_key);
        if ($phone_count >= 5) {
            return new WP_Error('phone_locked', 'این شماره تلفن موقتاً به دلیل درخواست‌های زیاد برای ۱ ساعت قفل شده است.');
        }
        set_transient($phone_count_key, $phone_count + 1, HOUR_IN_SECONDS);

        // Set 120s cooldown
        set_transient($transient_cooldown, 1, 120);

        return true;
    }
}
