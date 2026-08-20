<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Shahkar {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function verify($phone, $national_code) {
        $phone = Serene_Panel_Auth::normalize_phone($phone);
        $national_code = preg_replace('/[^\d]/', '', $national_code);

        // Validation rule for Iranian national ID
        if (strlen($national_code) !== 10) {
            return new WP_Error('invalid_national_code', 'کد ملی باید دقیقاً ۱۰ رقم باشد.');
        }

        // Mock verification or external API hook
        $is_valid = apply_filters('serene_shahkar_verify_external', true, $phone, $national_code);

        if ($is_valid) {
            return true;
        }

        return new WP_Error('shahkar_mismatch', 'کد ملی وارد شده با مالک شماره تماس تطابق ندارد.');
    }
}
