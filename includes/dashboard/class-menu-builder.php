<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Menu_Builder {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function get_default_menu_items() {
        return [
            'dashboard' => [
                'id'       => 'dashboard',
                'title'    => 'پیشخوان',
                'icon'     => 'dashboard',
                'type'     => 'endpoint',
                'target'   => 'dashboard',
                'badge'    => '',
                'roles'    => 'all',
                'enabled'  => 1,
                'order'    => 1,
            ],
            'orders' => [
                'id'       => 'orders',
                'title'    => 'سفارش‌ها',
                'icon'     => 'shopping_bag',
                'type'     => 'endpoint',
                'target'   => 'orders',
                'badge'    => '',
                'roles'    => 'all',
                'enabled'  => 1,
                'order'    => 2,
            ],
            'wallet' => [
                'id'       => 'wallet',
                'title'    => 'کیف پول و پadash',
                'icon'     => 'account_balance_wallet',
                'type'     => 'endpoint',
                'target'   => 'wallet',
                'badge'    => '',
                'roles'    => 'all',
                'enabled'  => 1,
                'order'    => 3,
            ],
            'tickets' => [
                'id'       => 'tickets',
                'title'    => 'پیام‌ها و تیکت‌ها',
                'icon'     => 'chat',
                'type'     => 'endpoint',
                'target'   => 'tickets',
                'badge'    => '',
                'roles'    => 'all',
                'enabled'  => 1,
                'order'    => 4,
            ],
            'lucky_wheel' => [
                'id'       => 'lucky_wheel',
                'title'    => 'گردونه شانس',
                'icon'     => 'casino',
                'type'     => 'endpoint',
                'target'   => 'lucky_wheel',
                'badge'    => 'جایزه',
                'roles'    => 'all',
                'enabled'  => 1,
                'order'    => 5,
            ],
            'rewards' => [
                'id'       => 'rewards',
                'title'    => 'جوایز و هدایای من',
                'icon'     => 'card_giftcard',
                'type'     => 'endpoint',
                'target'   => 'rewards',
                'badge'    => 'هدیه',
                'roles'    => 'all',
                'enabled'  => 1,
                'order'    => 6,
            ],
            'affiliate' => [
                'id'       => 'affiliate',
                'title'    => 'همکاری در فروش',
                'icon'     => 'handshake',
                'type'     => 'endpoint',
                'target'   => 'affiliate',
                'badge'    => 'درآمد',
                'roles'    => 'all',
                'enabled'  => 1,
                'order'    => 7,
            ],
            'downloads' => [
                'id'       => 'downloads',
                'title'    => 'دانلودها و لایسنس‌ها',
                'icon'     => 'cloud_download',
                'type'     => 'endpoint',
                'target'   => 'downloads',
                'badge'    => '',
                'roles'    => 'all',
                'enabled'  => 1,
                'order'    => 7,
            ],
            'profile' => [
                'id'       => 'profile',
                'title'    => 'اطلاعات حساب',
                'icon'     => 'person',
                'type'     => 'endpoint',
                'target'   => 'profile',
                'badge'    => '',
                'roles'    => 'all',
                'enabled'  => 1,
                'order'    => 7,
            ],
            'addresses' => [
                'id'       => 'addresses',
                'title'    => 'آدرس‌های من',
                'icon'     => 'location_on',
                'type'     => 'endpoint',
                'target'   => 'addresses',
                'badge'    => '',
                'roles'    => 'all',
                'enabled'  => 1,
                'order'    => 8,
            ],
        ];
    }

    public static function get_menu_items() {
        $custom_menu = get_option('serene_panel_custom_menu', null);
        $defaults    = self::get_default_menu_items();

        if (!$custom_menu || !is_array($custom_menu) || empty($custom_menu)) {
            return $defaults;
        }

        // Auto-merge any newly added standard features (e.g. 'rewards', 'affiliate')
        foreach ($defaults as $k => $def) {
            if (!isset($custom_menu[$k])) {
                $custom_menu[$k] = $def;
            }
        }

        // Sort by order
        uasort($custom_menu, function($a, $b) {
            return intval($a['order'] ?? 0) <=> intval($b['order'] ?? 0);
        });

        return $custom_menu;
    }

    public static function get_user_visible_menu_items($user_id = 0) {
        $items = self::get_menu_items();
        $user = $user_id ? get_user_by('ID', $user_id) : wp_get_current_user();
        $user_roles = $user && !empty($user->roles) ? $user->roles : ['guest'];

        $filtered = [];
        foreach ($items as $key => $item) {
            if (empty($item['enabled'])) continue;

            $role_restriction = $item['roles'] ?? 'all';
            if ($role_restriction !== 'all' && !in_array($role_restriction, $user_roles)) {
                if ($role_restriction === 'administrator' && !in_array('administrator', $user_roles)) {
                    continue;
                }
            }
            $filtered[$key] = $item;
        }

        return $filtered;
    }
}
