<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Profile {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('get_avatar_url', [$this, 'custom_avatar_url'], 10, 3);
    }

    public function custom_avatar_url($url, $id_or_email, $args) {
        $user_id = 0;
        if (is_numeric($id_or_email)) {
            $user_id = (int) $id_or_email;
        } elseif (is_object($id_or_email) && isset($id_or_email->user_id)) {
            $user_id = (int) $id_or_email->user_id;
        } elseif (is_string($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
            if ($user) $user_id = $user->ID;
        }

        $options = get_option('serene_panel_options', []);
        $site_default = !empty($options['default_avatar_url']) ? $options['default_avatar_url'] : '';

        if ($user_id) {
            $custom_avatar = get_user_meta($user_id, '_serene_avatar_url', true);
            if ($custom_avatar) {
                return esc_url($custom_avatar);
            }

            if (!empty($site_default)) {
                return esc_url($site_default);
            }

            // Generate stylish initial or default avatar
            $user = get_user_by('ID', $user_id);
            $initial = 'U';
            if ($user) {
                $name = $user->display_name ?: $user->user_login;
                $initial = mb_substr($name, 0, 1, 'UTF-8');
            }

            $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100"><defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#4c5e8b"/><stop offset="100%" stop-color="#1e293b"/></linearGradient></defs><rect width="100" height="100" rx="30" fill="url(#g)"/><text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="sans-serif" font-size="42" font-weight="900">' . esc_html($initial) . '</text></svg>';
            return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
        }

        if (!empty($site_default)) {
            return esc_url($site_default);
        }

        $default_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100"><rect width="100" height="100" rx="30" fill="#e2e8f0"/><circle cx="50" cy="40" r="18" fill="#94a3b8"/><path d="M22 84 C22 66 35 58 50 58 C65 58 78 66 78 84 Z" fill="#94a3b8"/></svg>';
        return 'data:image/svg+xml;utf8,' . rawurlencode($default_svg);
    }

    public static function save_avatar($user_id, $file_data) {
        if (empty($file_data)) {
            return new WP_Error('no_file', 'فایلی ارسال نشده است.');
        }

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $upload_overrides = ['test_form' => false];
        $movefile = wp_handle_upload($file_data, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            update_user_meta($user_id, '_serene_avatar_url', $movefile['url']);
            return $movefile['url'];
        }

        return new WP_Error('upload_error', $movefile['error']);
    }
}
