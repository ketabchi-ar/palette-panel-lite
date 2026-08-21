<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_System_Logger {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function log($level, $channel, $message, $context = []) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_system_logs';

        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            return false;
        }

        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        $ip = Serene_Panel_Rate_Limiter::get_client_ip();

        return $wpdb->insert($table, [
            'level'      => strtoupper(sanitize_text_field($level)),
            'channel'    => strtoupper(sanitize_text_field($channel)),
            'message'    => sanitize_textarea_field($message),
            'context'    => !empty($context) ? wp_json_encode($context) : null,
            'user_id'    => (int) $user_id,
            'ip_address' => $ip,
            'created_at' => current_time('mysql'),
        ]);
    }

    public static function info($channel, $message, $context = []) {
        return self::log('INFO', $channel, $message, $context);
    }

    public static function error($channel, $message, $context = []) {
        return self::log('ERROR', $channel, $message, $context);
    }

    public static function warning($channel, $message, $context = []) {
        return self::log('WARNING', $channel, $message, $context);
    }

    public static function success($channel, $message, $context = []) {
        return self::log('SUCCESS', $channel, $message, $context);
    }

    public static function get_logs($limit = 100, $channel = '', $level = '', $search = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_system_logs';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            return [];
        }

        $where = ["1=1"];
        $params = [];

        if (!empty($channel) && $channel !== 'ALL') {
            $where[] = "channel = %s";
            $params[] = $channel;
        }

        if (!empty($level) && $level !== 'ALL') {
            $where[] = "level = %s";
            $params[] = $level;
        }

        if (!empty($search)) {
            $where[] = "(message LIKE %s OR ip_address LIKE %s)";
            $params[] = '%' . $wpdb->esc_like($search) . '%';
            $params[] = '%' . $wpdb->esc_like($search) . '%';
        }

        $where_sql = implode(' AND ', $where);
        $sql = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY id DESC LIMIT %d";
        $params[] = $limit;

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    public static function clear_logs() {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_system_logs';
        return $wpdb->query("TRUNCATE TABLE {$table}");
    }
}
