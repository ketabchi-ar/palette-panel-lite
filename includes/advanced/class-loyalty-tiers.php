<?php
if (!defined('ABSPATH')) {
    exit;
}

class Palette_Panel_Loyalty_Tiers {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_tier_discount']);
    }

    public static function get_default_tiers() {
        return [
            'bronze' => [
                'id'               => 'bronze',
                'name'             => 'برنزی',
                'icon'             => 'stars',
                'color'            => '#cd7f32',
                'min_spent'        => 0,
                'lookback_months'  => 0,
                'min_orders'       => 0,
                'discount'         => 0,
                'cashback_percent' => 1,
                'perks'            => 'دریافت ۱٪ پاداش نقدی در کیف پول',
            ],
            'silver' => [
                'id'               => 'silver',
                'name'             => 'نقره‌ای',
                'icon'             => 'workspace_premium',
                'color'            => '#94a3b8',
                'min_spent'        => 2000000,
                'lookback_months'  => 6,
                'min_orders'       => 2,
                'discount'         => 3,
                'cashback_percent' => 3,
                'perks'            => '۳٪ تخفیف روی سفارشات + ۳٪ پاداش کش‌بک',
            ],
            'gold' => [
                'id'               => 'gold',
                'name'             => 'طلایی',
                'icon'             => 'military_tech',
                'color'            => '#f59e0b',
                'min_spent'        => 8000000,
                'lookback_months'  => 6,
                'min_orders'       => 5,
                'discount'         => 7,
                'cashback_percent' => 5,
                'perks'            => 'ارسال رایگان + ۷٪ تخفیف روی تمامی محصولات',
            ],
            'diamond' => [
                'id'               => 'diamond',
                'name'             => 'الماس VIP',
                'icon'             => 'diamond',
                'color'            => '#06b6d4',
                'min_spent'        => 25000000,
                'lookback_months'  => 6,
                'min_orders'       => 10,
                'discount'         => 12,
                'cashback_percent' => 8,
                'perks'            => '۱۲٪ تخفیف ویژه + ۸٪ پاداش کش‌بک + هدیه اختصاصی',
            ],
        ];
    }

    public static function get_tiers() {
        $saved = get_option('serene_panel_loyalty_tiers', null);
        if (!empty($saved) && is_array($saved)) {
            return $saved;
        }
        return self::get_default_tiers();
    }

    public static function save_tiers($tiers) {
        return update_option('serene_panel_loyalty_tiers', $tiers);
    }

    public static function calculate_user_spent_and_orders($user_id, $months = 0) {
        global $wpdb;
        $customer_orders = wc_get_orders([
            'customer_id' => $user_id,
            'status'      => ['completed', 'processing'],
            'limit'       => -1,
            'date_after'  => ($months > 0) ? date('Y-m-d H:i:s', strtotime("-{$months} months")) : null,
        ]);

        $total_spent = 0;
        $order_count = count($customer_orders);

        foreach ($customer_orders as $order) {
            $total_spent += floatval($order->get_total());
        }

        return [
            'spent'  => $total_spent,
            'orders' => $order_count,
        ];
    }

    public static function get_user_tier($user_id) {
        $tiers = self::get_tiers();
        if (empty($tiers)) {
            $tiers = self::get_default_tiers();
        }

        // Sort descending by min_spent
        uasort($tiers, function($a, $b) {
            return ($b['min_spent'] ?? 0) <=> ($a['min_spent'] ?? 0);
        });

        $opt = get_option('serene_panel_options', []);
        if (empty($opt['enable_loyalty_tiers'])) {
            $first = reset($tiers);
            return $first ?: ['name' => 'عادی', 'discount' => 0, 'icon' => 'stars', 'color' => '#64748b'];
        }

        foreach ($tiers as $tier) {
            $months = intval($tier['lookback_months'] ?? 0);
            $stats = self::calculate_user_spent_and_orders($user_id, $months);

            $spent_qualified = ($stats['spent'] >= floatval($tier['min_spent'] ?? 0));
            $orders_qualified = ($stats['orders'] >= intval($tier['min_orders'] ?? 0));

            if ($spent_qualified && $orders_qualified) {
                $tier['current_spent'] = $stats['spent'];
                $tier['current_orders'] = $stats['orders'];
                return $tier;
            }
        }

        $fallback = end($tiers);
        return $fallback ?: ['name' => 'برنزی', 'discount' => 0, 'icon' => 'stars', 'color' => '#cd7f32'];
    }

    public function apply_tier_discount($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;
        if (!is_user_logged_in()) return;

        $tier = self::get_user_tier(get_current_user_id());
        $discount_pct = floatval($tier['discount'] ?? 0);
        if ($discount_pct > 0) {
            $discount = ($cart->get_subtotal() * $discount_pct) / 100;
            $cart->add_fee(sprintf('تخفیف باشگاه مشتریان (سطح %s - %d٪)', $tier['name'], $discount_pct), -$discount);
        }
    }
}

// Backward Compatibility Class Alias
class Serene_Panel_Loyalty_Tiers extends Palette_Panel_Loyalty_Tiers {}
