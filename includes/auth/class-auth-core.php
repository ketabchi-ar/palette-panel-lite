<?php
if (!defined('ABSPATH')) {
    exit;
}

class Palette_Panel_Auth {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'handle_login_redirect']);
        add_shortcode('palette_login', [$this, 'render_login_shortcode']);
        add_shortcode('serene_login', [$this, 'render_login_shortcode']);
    }

    public function handle_login_redirect() {
        global $pagenow;
        if ($pagenow === 'wp-login.php' && !isset($_GET['admin_standard']) && !isset($_GET['action'])) {
            $opt = get_option('serene_panel_options', []);
            if (!empty($opt['enable_custom_login_redirect'])) {
                $target = $opt['login_role_target'] ?? 'all';
                
                // If customers_only and current logged in is admin, ignore
                if ($target === 'customers_only' && current_user_can('manage_options')) {
                    return;
                }

                $login_url = !empty($opt['custom_login_url']) ? esc_url($opt['custom_login_url']) : wc_get_page_permalink('myaccount');
                wp_safe_redirect($login_url);
                exit;
            }
        }
    }

    public function render_login_shortcode($atts) {
        if (is_user_logged_in()) {
            return '<div class="p-6 bg-surface-container rounded-2xl text-center text-xs font-bold">شما قبلاً وارد سیستم شده‌اید. <a href="' . esc_url(wc_get_page_permalink('myaccount')) . '" class="text-primary underline">مشاهده پیشخوان</a></div>';
        }
        ob_start();
        include SERENE_PANEL_TEMPLATES_PATH . 'auth/login-page.php';
        return ob_get_clean();
    }

    public static function normalize_phone($phone) {
        $phone = preg_replace('/[^\d]/', '', $phone);
        // Convert Persian/Arabic digits to English
        $farsi = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $latin = ['0','1','2','3','4','5','6','7','8','9'];
        $phone = str_replace($farsi, $latin, $phone);

        if (strpos($phone, '0098') === 0) {
            $phone = substr($phone, 4);
        } elseif (strpos($phone, '98') === 0) {
            $phone = substr($phone, 2);
        } elseif (strpos($phone, '0') === 0) {
            $phone = substr($phone, 1);
        }

        return '0' . $phone;
    }

    public static function is_valid_iran_phone($phone) {
        $phone = self::normalize_phone($phone);
        return preg_match('/^09[0-9]{9}$/', $phone);
    }

    public static function get_user_by_phone($phone) {
        $phone = self::normalize_phone($phone);
        
        $users = get_users([
            'meta_key'     => '_serene_phone',
            'meta_value'   => $phone,
            'number'       => 1,
            'fields'       => 'all',
        ]);

        if (!empty($users)) {
            return $users[0];
        }

        // Fallback check billing_phone
        $users = get_users([
            'meta_key'     => 'billing_phone',
            'meta_value'   => $phone,
            'number'       => 1,
            'fields'       => 'all',
        ]);

        if (!empty($users)) {
            return $users[0];
        }

        // Check username matching phone
        return get_user_by('login', $phone);
    }

    public static function create_or_get_user_by_phone($phone) {
        $phone = self::normalize_phone($phone);
        $user = self::get_user_by_phone($phone);

        if ($user) {
            return $user;
        }

        // Create new user
        $username = $phone;
        $password = wp_generate_password(18, true, true);
        $email    = $phone . '@' . parse_url(home_url(), PHP_URL_HOST);

        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            return $user_id;
        }

        // Assign customer role
        $user = get_user_by('ID', $user_id);
        $user->set_role('customer');

        update_user_meta($user_id, '_serene_phone', $phone);
        update_user_meta($user_id, 'billing_phone', $phone);
        update_user_meta($user_id, 'first_name', 'کاربر');
        update_user_meta($user_id, 'last_name', substr($phone, -4));

        do_action('serene_user_registered', $user_id, $phone);

        return $user;
    }

    public static function login_user($user_id, $remember = true) {
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, $remember);

        // Record active session
        if (class_exists('Serene_Panel_Session_Manager')) {
            Serene_Panel_Session_Manager::record_session($user_id);
        }

        do_action('wp_login', get_user_by('ID', $user_id)->user_login, get_user_by('ID', $user_id));
        return true;
    }
}

// Backward Compatibility Class Alias
class Serene_Panel_Auth extends Palette_Panel_Auth {}
