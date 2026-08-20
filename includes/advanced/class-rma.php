<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_RMA {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function create_request($user_id, $order_id, $product_id, $reason, $images = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_rma_requests';

        return $wpdb->insert($table, [
            'user_id'    => (int) $user_id,
            'order_id'   => (int) $order_id,
            'product_id' => (int) $product_id,
            'reason'     => sanitize_textarea_field($reason),
            'status'     => 'pending',
            'images'     => esc_url_raw($images),
            'created_at' => current_time('mysql'),
        ]);
    }
}
