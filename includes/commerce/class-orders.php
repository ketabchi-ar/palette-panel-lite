<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Orders {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function get_user_orders($user_id, $status = 'all', $limit = 10, $page = 1) {
        if (!function_exists('wc_get_orders')) {
            return [];
        }

        $args = [
            'customer_id' => $user_id,
            'limit'       => $limit,
            'page'        => $page,
            'paginate'    => true,
            'orderby'     => 'date',
            'order'       => 'DESC',
        ];

        if ($status !== 'all') {
            $args['status'] = $status;
        }

        return wc_get_orders($args);
    }

    public static function get_status_badge($status) {
        $badges = [
            'completed'  => ['label' => 'تکمیل شده', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            'processing' => ['label' => 'در حال پردازش', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'pending'    => ['label' => 'در انتظار پرداخت', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
            'on-hold'    => ['label' => 'در انتظار بررسی', 'class' => 'bg-orange-50 text-orange-700 border-orange-200'],
            'cancelled'  => ['label' => 'لغو شده', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
            'refunded'   => ['label' => 'مسترد شده', 'class' => 'bg-purple-50 text-purple-700 border-purple-200'],
            'failed'     => ['label' => 'ناموفق', 'class' => 'bg-red-50 text-red-700 border-red-200'],
        ];

        return isset($badges[$status]) ? $badges[$status] : ['label' => $status, 'class' => 'bg-gray-50 text-gray-700 border-gray-200'];
    }
}
