<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Lucky_Wheel {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function get_default_slices() {
        return [
            ['id' => 0, 'label' => 'کد تخفیف ۲۰٪',   'type' => 'coupon', 'value' => '20%',      'weight' => 20, 'color' => '#4c5e8b', 'text' => '#ffffff'],
            ['id' => 1, 'label' => '۵۰,۰۰۰ ت شارژ',    'type' => 'wallet', 'value' => '50000',    'weight' => 15, 'color' => '#6b5680', 'text' => '#ffffff'],
            ['id' => 2, 'label' => 'پوچ! دوباره بچرخ', 'type' => 'empty',  'value' => '0',        'weight' => 30, 'color' => '#dfe2ed', 'text' => '#2f323b'],
            ['id' => 3, 'label' => 'ارسال رایگان',     'type' => 'coupon', 'value' => 'freeship', 'weight' => 20, 'color' => '#b6c8fc', 'text' => '#192d57'],
            ['id' => 4, 'label' => '۱۰۰,۰۰۰ ت شارژ',   'type' => 'wallet', 'value' => '100000',   'weight' => 5,  'color' => '#40527f', 'text' => '#ffffff'],
            ['id' => 5, 'label' => 'کد تخفیف ۱۰٪',   'type' => 'coupon', 'value' => '10%',      'weight' => 10, 'color' => '#585f72', 'text' => '#ffffff'],
        ];
    }

    public static function get_wheel_slices() {
        $slices = get_option('serene_lucky_wheel_slices', null);
        if (!$slices || !is_array($slices) || empty($slices)) {
            return self::get_default_slices();
        }
        return $slices;
    }

    public static function can_user_spin($user_id) {
        if (!$user_id) return false;
        
        global $wpdb;
        $table = $wpdb->prefix . 'serene_lucky_wheel_spins';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            return true;
        }

        $today = current_time('Y-m-d 00:00:00');
        $spins_today = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d AND created_at >= %s",
            $user_id, $today
        ));

        $options = get_option('serene_panel_options', []);
        $daily_limit = isset($options['lucky_wheel_daily_limit']) ? (int) $options['lucky_wheel_daily_limit'] : 1;

        return $spins_today < $daily_limit;
    }

    public static function create_dynamic_coupon($user_id, $val_str, $label) {
        $options = get_option('serene_panel_options', []);
        $expiry_days = !empty($options['lucky_wheel_coupon_expiry_days']) ? intval($options['lucky_wheel_coupon_expiry_days']) : 7;
        if ($expiry_days < 1) $expiry_days = 7;

        if (!class_exists('WC_Coupon')) {
            return [
                'code'        => sanitize_text_field($val_str),
                'expires_at'  => date('Y-m-d H:i:s', strtotime("+{$expiry_days} days")),
                'expiry_days' => $expiry_days
            ];
        }

        $clean_val = trim($val_str);
        $user = get_user_by('ID', $user_id);
        $user_email = $user ? $user->user_email : '';

        $discount_type = 'percent';
        $amount = 10;
        $is_freeship = false;

        if (stripos($clean_val, 'freeship') !== false || stripos($label, 'ارسال رایگان') !== false) {
            $is_freeship = true;
            $amount = 0;
        } elseif (strpos($clean_val, '%') !== false || (is_numeric($clean_val) && intval($clean_val) <= 100 && intval($clean_val) > 0)) {
            $discount_type = 'percent';
            $amount = floatval(str_replace('%', '', $clean_val));
        } else {
            $num = floatval(preg_replace('/[^0-9.]/', '', $clean_val));
            if ($num > 100) {
                $discount_type = 'fixed_cart';
                $amount = $num;
            } else {
                $discount_type = 'percent';
                $amount = $num > 0 ? $num : 10;
            }
        }

        $random_suffix = strtoupper(wp_generate_password(5, false, false));
        $prefix = $is_freeship ? 'SHIP' : 'WIN' . intval($amount);
        $coupon_code = $prefix . '-' . $random_suffix;
        $expires_timestamp = strtotime("+{$expiry_days} days");

        $coupon = new WC_Coupon();
        $coupon->set_code($coupon_code);
        $coupon->set_description(sprintf('کوپن اختصاصی گردونه شانس برای کاربر #%d (%s) - اعتبار %d روز', $user_id, $label, $expiry_days));
        $coupon->set_discount_type($discount_type);
        $coupon->set_amount($amount);
        $coupon->set_individual_use(true);
        $coupon->set_usage_limit(1);
        $coupon->set_usage_limit_per_user(1);
        if ($user_email) {
            $coupon->set_email_restrictions([$user_email]);
        }
        if ($is_freeship) {
            $coupon->set_free_shipping(true);
        }
        $coupon->set_date_expires($expires_timestamp);
        $coupon->save();

        if (class_exists('Serene_Panel_System_Logger')) {
            Serene_Panel_System_Logger::success('LUCKY_WHEEL', sprintf('ساخت کوپن اختصاصی ووکامرس %s با اعتبار %d روز برای کاربر #%d', $coupon_code, $expiry_days, $user_id));
        }

        return [
            'code'        => $coupon_code,
            'expires_at'  => date('Y-m-d H:i:s', $expires_timestamp),
            'expiry_days' => $expiry_days,
        ];
    }

    public static function spin($user_id) {
        if (!$user_id) {
            return new WP_Error('not_logged_in', 'برای چرخش گردونه شانس ابتدا وارد حساب کاربری خود شوید.');
        }

        if (!self::can_user_spin($user_id)) {
            return new WP_Error('limit_reached', 'شما سهمیه چرخش امروز خود را استفاده کرده‌اید. لطفاً فردا مجدداً شانس خود را امتحان کنید!');
        }

        $slices = self::get_wheel_slices();
        $total_weight = 0;
        foreach ($slices as $s) {
            $total_weight += intval($s['weight'] ?? 10);
        }
        if ($total_weight <= 0) $total_weight = 100;

        $rand = wp_rand(1, $total_weight);
        $cumulative = 0;
        $winning_slice = $slices[0];

        foreach ($slices as $slice) {
            $cumulative += intval($slice['weight'] ?? 10);
            if ($rand <= $cumulative) {
                $winning_slice = $slice;
                break;
            }
        }

        $unique_coupon = '';
        $expires_at = null;
        $expiry_days = 7;

        // Process Prize
        if ($winning_slice['type'] === 'wallet' && floatval($winning_slice['value']) > 0) {
            Serene_Panel_Wallet::update_balance(
                $user_id,
                floatval($winning_slice['value']),
                'credit',
                sprintf('جایزه نقدی گردونه شانس (%s)', $winning_slice['label'])
            );
        } elseif ($winning_slice['type'] === 'coupon') {
            $coupon_data = self::create_dynamic_coupon($user_id, $winning_slice['value'], $winning_slice['label']);
            $unique_coupon = $coupon_data['code'];
            $expires_at = $coupon_data['expires_at'];
            $expiry_days = $coupon_data['expiry_days'];
        }

        // Record Spin in DB
        global $wpdb;
        $table = $wpdb->prefix . 'serene_lucky_wheel_spins';
        $wpdb->insert($table, [
            'user_id'     => (int) $user_id,
            'prize_type'  => sanitize_text_field($winning_slice['type']),
            'prize_value' => sanitize_text_field($winning_slice['label']),
            'coupon_code' => $unique_coupon ?: sanitize_text_field($winning_slice['value']),
            'expires_at'  => $expires_at,
            'ip_address'  => Serene_Panel_Rate_Limiter::get_client_ip(),
            'created_at'  => current_time('mysql'),
        ]);

        return [
            'slice_index' => intval($winning_slice['id']),
            'prize'       => $winning_slice,
            'label'       => $winning_slice['label'],
            'coupon_code' => $unique_coupon,
            'expires_at'  => $expires_at,
            'expiry_days' => $expiry_days,
        ];
    }

    public static function get_user_rewards($user_id) {
        if (!$user_id) return [];

        global $wpdb;
        $table = $wpdb->prefix . 'serene_lucky_wheel_spins';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            return [];
        }

        $records = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND prize_type != 'empty' ORDER BY created_at DESC",
            $user_id
        ));

        $rewards = [];
        foreach ($records as $r) {
            $status = 'active';
            $status_label = 'معتبر';
            $status_class = 'bg-emerald-100 text-emerald-800 border-emerald-300';

            $expires_at = !empty($r->expires_at) ? $r->expires_at : null;

            if ($r->prize_type === 'coupon' && !empty($r->coupon_code)) {
                if (class_exists('WC_Coupon')) {
                    $c = new WC_Coupon($r->coupon_code);
                    if ($c->get_id()) {
                        $usage_count = $c->get_usage_count();
                        $date_expires = $c->get_date_expires();
                        if ($date_expires && !$expires_at) {
                            $expires_at = date('Y-m-d H:i:s', $date_expires->getTimestamp());
                        }
                        if ($usage_count > 0) {
                            $status = 'used';
                            $status_label = 'استفاده شده';
                            $status_class = 'bg-slate-100 text-slate-700 border-slate-300';
                        } elseif ($date_expires && $date_expires->getTimestamp() < current_time('timestamp')) {
                            $status = 'expired';
                            $status_label = 'منقضی شده';
                            $status_class = 'bg-rose-100 text-rose-800 border-rose-300';
                        }
                    }
                }
            } elseif ($r->prize_type === 'wallet') {
                $status = 'credited';
                $status_label = 'واریز شده به کیف پول';
                $status_class = 'bg-purple-100 text-purple-800 border-purple-300';
            }

            $rewards[] = [
                'id'           => $r->id,
                'type'         => $r->prize_type,
                'title'        => $r->prize_value,
                'code'         => $r->coupon_code,
                'status'       => $status,
                'status_label' => $status_label,
                'status_class' => $status_class,
                'expires_at'   => $expires_at,
                'created_at'   => $r->created_at,
            ];
        }

        return $rewards;
    }
}
