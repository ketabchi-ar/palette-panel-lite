<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Wallet {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('woocommerce_payment_gateways', [$this, 'register_wallet_gateway']);
        add_action('woocommerce_order_status_completed', [$this, 'process_cashback_reward'], 10, 1);
    }

    public function register_wallet_gateway($gateways) {
        if (!self::is_woo_wallet_active()) {
            $gateways[] = 'WC_Gateway_Serene_Wallet';
        }
        return $gateways;
    }

    public static function is_woo_wallet_active() {
        return function_exists('woo_wallet') || class_exists('Woo_Wallet_Wallet') || function_exists('woo_wallet_get_user_wallet_balance') || defined('WOO_WALLET_VERSION');
    }

    public static function get_balance($user_id) {
        if (!$user_id) return 0.00;

        if (self::is_woo_wallet_active()) {
            if (function_exists('woo_wallet_get_user_wallet_balance')) {
                return (float) woo_wallet_get_user_wallet_balance($user_id, 'edit');
            } elseif (function_exists('woo_wallet') && isset(woo_wallet()->wallet)) {
                return (float) woo_wallet()->wallet->get_wallet_balance($user_id, 'edit');
            }
        }

        return (float) get_user_meta($user_id, '_serene_wallet_balance', true) ?: 0.00;
    }

    public static function update_balance($user_id, $amount, $type, $description, $order_id = 0) {
        if (!$user_id) return new WP_Error('invalid_user', 'کاربر نامعتبر است.');

        $abs_amount = abs(floatval($amount));
        if ($abs_amount <= 0) return self::get_balance($user_id);

        $is_debit = (in_array(strtolower($type), ['debit', 'withdraw', 'transfer_out', 'deduct']) || floatval($amount) < 0);
        $signed_amount = $is_debit ? -$abs_amount : $abs_amount;
        $db_type = $is_debit ? 'debit' : 'credit';

        if (self::is_woo_wallet_active()) {
            if (function_exists('woo_wallet') && isset(woo_wallet()->wallet)) {
                if ($is_debit) {
                    $current = self::get_balance($user_id);
                    if ($current < $abs_amount) {
                        return new WP_Error('insufficient_funds', 'موجودی کیف پول شما کافی نمی‌باشد.');
                    }
                    woo_wallet()->wallet->debit($user_id, $abs_amount, $description);
                } else {
                    woo_wallet()->wallet->credit($user_id, $abs_amount, $description);
                }
                Serene_Panel_System_Logger::info('WALLET', sprintf('تراکنش Woo-Wallet برای کاربر #%d (نوع: %s, مبلغ: %s): %s', $user_id, $db_type, $abs_amount, $description));
                return self::get_balance($user_id);
            }
        }

        global $wpdb;
        $table = $wpdb->prefix . 'serene_wallet_tx';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            Serene_Panel_Activator::create_tables();
        }

        $current = self::get_balance($user_id);
        $new_balance = $current + $signed_amount;

        if ($new_balance < 0) {
            return new WP_Error('insufficient_funds', 'موجودی کیف پول کافی نیست.');
        }

        update_user_meta($user_id, '_serene_wallet_balance', $new_balance);

        $wpdb->insert($table, [
            'user_id'       => (int) $user_id,
            'type'          => sanitize_text_field($db_type),
            'amount'        => (float) $signed_amount,
            'balance_after' => (float) $new_balance,
            'description'   => sanitize_text_field($description),
            'order_id'      => (int) $order_id,
            'created_at'    => current_time('mysql'),
        ]);

        Serene_Panel_System_Logger::info('WALLET', sprintf('تراکنش کیف پول داخلی برای کاربر #%d (مبلغ: %s): %s', $user_id, $signed_amount, $description));

        return $new_balance;
    }

    public static function get_transactions($user_id, $limit = 30) {
        global $wpdb;
        $transactions = [];

        if (self::is_woo_wallet_active()) {
            $table = $wpdb->prefix . 'woo_wallet_transactions';
            if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") === $table) {
                $rows = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM {$table} WHERE user_id = %d ORDER BY 1 DESC LIMIT %d",
                    $user_id, $limit
                ));
                if (!empty($rows)) {
                    foreach ($rows as $r) {
                        $is_credit = (isset($r->type) && $r->type === 'credit');
                        $item = new stdClass();
                        $item->id = $r->transaction_id ?? ($r->id ?? 0);
                        $item->user_id = $r->user_id;
                        $item->type = $r->type ?? ($is_credit ? 'credit' : 'debit');
                        $item->amount = $is_credit ? abs(floatval($r->amount)) : -abs(floatval($r->amount));
                        $item->balance_after = floatval($r->balance ?? 0);
                        $item->description = $r->details ?? ($r->description ?? 'تراکنش کیف پول');
                        $item->created_at = $r->date ?? ($r->created_at ?? current_time('mysql'));
                        $transactions[] = $item;
                    }
                    return $transactions;
                }
            }
        }

        $table = $wpdb->prefix . 'serene_wallet_tx';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") === $table) {
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d ORDER BY id DESC LIMIT %d",
                $user_id, $limit
            ));
            if (!empty($rows)) {
                return $rows;
            }
        }

        return [];
    }

    public function process_cashback_reward($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) return;

        $user_id = $order->get_customer_id();
        if (!$user_id) return;

        if (get_post_meta($order_id, '_serene_cashback_awarded', true)) {
            return;
        }

        $options = get_option('serene_panel_options', []);
        $percent = isset($options['cashback_percent']) ? floatval($options['cashback_percent']) : 5;

        if ($percent <= 0) return;

        $total = $order->get_total();
        $cashback = ($total * $percent) / 100;

        self::update_balance(
            $user_id,
            $cashback,
            'cashback',
            sprintf('پاداش نقدی (کش‌بک %s٪) سفارش #%s', $percent, $order->get_order_number()),
            $order_id
        );

        update_post_meta($order_id, '_serene_cashback_awarded', $cashback);
    }
}

// WooCommerce Payment Gateway Class for Serene Wallet (used when woo-wallet not active)
add_action('plugins_loaded', function() {
    if (!class_exists('WC_Payment_Gateway')) return;

    class WC_Gateway_Serene_Wallet extends WC_Payment_Gateway {
        public function __construct() {
            $this->id                 = 'serene_wallet';
            $this->icon               = '';
            $this->has_fields         = false;
            $this->method_title       = 'پرداخت از کیف پول (Serene)';
            $this->method_description = 'امکان پرداخت مستقیم و سریع سفارش از موجودی کیف پول کاربر';

            $this->init_form_fields();
            $this->init_settings();

            $this->title       = $this->get_option('title', 'پرداخت با کیف پول');
            $this->description = $this->get_option('description', 'کسر مبلغ از موجودی حساب کاربری شما');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
        }

        public function init_form_fields() {
            $this->form_fields = [
                'enabled' => [
                    'title'   => 'فعالسازی',
                    'type'    => 'checkbox',
                    'label'   => 'فعالسازی درگاه کیف پول',
                    'default' => 'yes',
                ],
                'title' => [
                    'title'   => 'عنوان',
                    'type'    => 'text',
                    'default' => 'کیف پول شارژی',
                ],
            ];
        }

        public function is_available() {
            if (!is_user_logged_in()) return false;
            $user_id = get_current_user_id();
            $balance = Serene_Panel_Wallet::get_balance($user_id);
            $total   = WC()->cart ? WC()->cart->total : 0;
            return ($balance >= $total) && parent::is_available();
        }

        public function process_payment($order_id) {
            $order   = wc_get_order($order_id);
            $user_id = $order->get_customer_id();
            $total   = $order->get_total();

            $res = Serene_Panel_Wallet::update_balance(
                $user_id,
                -$total,
                'debit',
                sprintf('پرداخت سفارش شماره #%s از کیف پول', $order->get_order_number()),
                $order_id
            );

            if (is_wp_error($res)) {
                wc_add_notice($res->get_error_message(), 'error');
                return;
            }

            $order->payment_complete();
            WC()->cart->empty_cart();

            return [
                'result'   => 'success',
                'redirect' => $this->get_return_url($order),
            ];
        }
    }
});
