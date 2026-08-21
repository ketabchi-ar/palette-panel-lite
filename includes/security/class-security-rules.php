<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Security {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('serene_panel_can_send_otp', [$this, 'check_role_sms_permission'], 10, 2);
    }

    public function check_role_sms_permission($can_send, $phone_or_user_id) {
        $options = get_option('serene_panel_options', []);
        if (empty($options['disable_admin_sms'])) {
            return $can_send;
        }

        $user = null;
        if (is_numeric($phone_or_user_id)) {
            $user = get_user_by('ID', $phone_or_user_id);
        } else {
            $user = Serene_Panel_Auth::get_user_by_phone($phone_or_user_id);
        }

        if ($user && (in_array('administrator', (array) $user->roles) || in_array('shop_manager', (array) $user->roles))) {
            return false;
        }

        return $can_send;
    }

    public static function sanitize_input($data) {
        if (is_array($data)) {
            return array_map([__CLASS__, 'sanitize_input'], $data);
        }
        return sanitize_text_field(wp_unslash($data));
    }

    public static function verify_nonce($nonce_action = 'serene_panel_nonce') {
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : '';
        if (!wp_verify_nonce($nonce, $nonce_action)) {
            wp_send_json_error([
                'message' => 'توکن امنیتی نامعتبر است. لطفاً صفحه را مجدداً بارگذاری کنید.'
            ], 403);
            exit;
        }
    }
}
