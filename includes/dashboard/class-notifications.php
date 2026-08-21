<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Notifications {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function create_notification($user_id, $title, $message, $type = 'info') {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_notifications';

        return $wpdb->insert($table, [
            'user_id'    => (int) $user_id,
            'title'      => sanitize_text_field($title),
            'message'    => sanitize_textarea_field($message),
            'type'       => sanitize_text_field($type),
            'is_read'    => 0,
            'created_at' => current_time('mysql'),
        ]);
    }

    public static function get_user_notifications($user_id, $limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_notifications';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d OR user_id = 0 ORDER BY id DESC LIMIT %d",
            $user_id, $limit
        ));
    }

    public static function get_unread_count($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_notifications';

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE (user_id = %d OR user_id = 0) AND is_read = 0",
            $user_id
        ));
    }
}
