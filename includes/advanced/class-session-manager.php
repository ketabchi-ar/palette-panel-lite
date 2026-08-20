<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Session_Manager {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'validate_current_session']);
    }

    public static function record_session($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_active_sessions';

        $token = wp_generate_password(48, false);
        $ip = Serene_Panel_Rate_Limiter::get_client_ip();
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : 'Unknown';

        $device = 'مرورگر دسکتاپ';
        if (wp_is_mobile()) {
            $device = 'گوشی موبایل';
        }

        $wpdb->insert($table, [
            'user_id'       => (int) $user_id,
            'session_token' => $token,
            'ip_address'    => $ip,
            'user_agent'    => $ua,
            'device_name'   => $device,
            'last_activity' => current_time('mysql'),
            'created_at'    => current_time('mysql'),
        ]);

        setcookie('serene_session_token', $token, time() + (30 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
    }

    public function validate_current_session() {
        if (!is_user_logged_in()) return;

        $token = isset($_COOKIE['serene_session_token']) ? sanitize_text_field($_COOKIE['serene_session_token']) : '';
        if (!$token) return;

        global $wpdb;
        $table = $wpdb->prefix . 'serene_active_sessions';

        $session = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE session_token = %s LIMIT 1",
            $token
        ));

        if ($session && $session->is_revoked == 1) {
            wp_logout();
            wp_safe_redirect(home_url('/panel/?session_killed=1'));
            exit;
        }
    }

    public static function get_user_sessions($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_active_sessions';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND is_revoked = 0 ORDER BY last_activity DESC",
            $user_id
        ));
    }

    public static function revoke_session($session_id, $user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_active_sessions';

        return $wpdb->update($table, [
            'is_revoked' => 1,
        ], [
            'id'      => (int) $session_id,
            'user_id' => (int) $user_id,
        ]);
    }

    public static function revoke_all_other_sessions($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_active_sessions';

        $current_token = isset($_COOKIE['serene_session_token']) ? sanitize_text_field($_COOKIE['serene_session_token']) : '';

        return $wpdb->query($wpdb->prepare(
            "UPDATE $table SET is_revoked = 1 WHERE user_id = %d AND session_token != %s",
            $user_id, $current_token
        ));
    }
}
