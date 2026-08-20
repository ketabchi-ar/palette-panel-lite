<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Affiliate {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'track_referral_cookie']);
        add_action('woocommerce_order_status_completed', [$this, 'reward_affiliate'], 20, 1);
    }

    public static function get_referral_code($user_id) {
        $code = get_user_meta($user_id, '_serene_ref_code', true);
        if (!$code) {
            $code = 'REF' . $user_id . strtoupper(wp_generate_password(4, false));
            update_user_meta($user_id, '_serene_ref_code', $code);
        }
        return $code;
    }

    public static function get_referral_link($user_id) {
        $code = self::get_referral_code($user_id);
        return add_query_arg('ref', $code, home_url('/'));
    }

    public function track_referral_cookie() {
        if (isset($_GET['ref'])) {
            $ref = sanitize_text_field($_GET['ref']);
            setcookie('serene_aff_ref', $ref, time() + (30 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
        }
    }

    public function reward_affiliate($order_id) {
        if (get_post_meta($order_id, '_serene_aff_rewarded', true)) return;

        $ref_code = isset($_COOKIE['serene_aff_ref']) ? sanitize_text_field($_COOKIE['serene_aff_ref']) : '';
        if (!$ref_code) return;

        $users = get_users([
            'meta_key'   => '_serene_ref_code',
            'meta_value' => $ref_code,
            'number'     => 1,
        ]);

        if (empty($users)) return;

        $aff_user = $users[0];
        $order = wc_get_order($order_id);
        if (!$order || $order->get_customer_id() === $aff_user->ID) return;

        $options = get_option('serene_panel_options', []);
        $comm_rate = isset($options['affiliate_commission']) ? floatval($options['affiliate_commission']) : 10;

        $reward = ($order->get_total() * $comm_rate) / 100;

        Serene_Panel_Wallet::update_balance(
            $aff_user->ID,
            $reward,
            'affiliate',
            sprintf('پورسانت همکاری در فروش از سفارش #%s', $order->get_order_number()),
            $order_id
        );

        update_post_meta($order_id, '_serene_aff_rewarded', 1);
    }

    public static function get_user_stats($user_id) {
        global $wpdb;
        $table_wallet = $wpdb->prefix . 'serene_wallet_transactions';
        
        $total_earnings = 0;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_wallet'") == $table_wallet) {
            $total_earnings = $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(amount) FROM $table_wallet WHERE user_id = %d AND type = 'credit' AND reason = 'affiliate'",
                $user_id
            )) ?: 0;
        }

        $code = self::get_referral_code($user_id);
        $total_sales = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = '_serene_aff_rewarded' AND meta_value = 1"
        )) ?: 0;

        $options = get_option('serene_panel_options', []);
        $rate = isset($options['affiliate_commission']) ? floatval($options['affiliate_commission']) : 10;

        return [
            'code'           => $code,
            'link'           => self::get_referral_link($user_id),
            'total_earnings' => $total_earnings,
            'rate'           => $rate,
        ];
    }
}
