<?php
/**
 * Plugin Name: پالت پنل لایت | پنل کاربری مدرن ووکامرس (Palette Panel Lite)
 * Plugin URI: https://palette.agency/
 * Description: نسخه رایگان و سبک پنل کاربری مدرن ووکامرس با دیزاین‌سیستم اختصاصی پالت و TailwindCSS. برای دسترسی به امکانات پیشرفته (ورود بیومتریک، کارت‌به‌کارت هوشمند، گردونه شانس و باشگاه مشتریان) نسخه کامل را از ژاکت تهیه فرمایید.
 * Version: 1.4.8
 * Author: آژانس دیجیتال پالت
 * Author URI: https://palette.agency/
 * Text Domain: palette-panel-lite
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('PALETTE_PANEL_VERSION')) {
    // Pro version is already active
    add_action('admin_notices', function() {
        echo '<div class="notice notice-warning is-dismissible"><p><strong>پالت پنل:</strong> نسخه حرفه‌ای (Pro) در حال حاضر فعال است، بنابراین نسخه لایت غیرفعال ماند.</p></div>';
    });
    return;
}

define('PALETTE_PANEL_LITE_VERSION', '1.4.8');
define('PALETTE_PANEL_IS_LITE', true);
define('PALETTE_PANEL_VERSION', '1.4.8');
define('SERENE_PANEL_VERSION', '1.4.8');

define('PALETTE_PANEL_FILE', __FILE__);
define('PALETTE_PANEL_PATH', plugin_dir_path(__FILE__));
define('PALETTE_PANEL_URL', plugin_dir_url(__FILE__));
define('PALETTE_PANEL_ASSETS_URL', PALETTE_PANEL_URL . 'assets/');
define('PALETTE_PANEL_TEMPLATES_PATH', PALETTE_PANEL_PATH . 'templates/');

if (!defined('SERENE_PANEL_FILE')) define('SERENE_PANEL_FILE', PALETTE_PANEL_FILE);
if (!defined('SERENE_PANEL_PATH')) define('SERENE_PANEL_PATH', PALETTE_PANEL_PATH);
if (!defined('SERENE_PANEL_URL')) define('SERENE_PANEL_URL', PALETTE_PANEL_URL);
if (!defined('SERENE_PANEL_ASSETS_URL')) define('SERENE_PANEL_ASSETS_URL', PALETTE_PANEL_ASSETS_URL);
if (!defined('SERENE_PANEL_TEMPLATES_PATH')) define('SERENE_PANEL_TEMPLATES_PATH', PALETTE_PANEL_TEMPLATES_PATH);

require_once PALETTE_PANEL_PATH . 'includes/class-autoloader.php';
require_once PALETTE_PANEL_PATH . 'includes/class-activator.php';
Palette_Panel_Autoloader::register();

final class Palette_Woo_Panel_Lite {
    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        register_activation_hook(__FILE__, ['Palette_Panel_Activator', 'activate']);
        add_action('init', [$this, 'init_components'], 5);
        add_action('init', [$this, 'register_panel_rewrite_rules'], 10);
        add_action('plugins_loaded', [$this, 'check_woocommerce']);
    }

    public function check_woocommerce() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', [$this, 'woocommerce_missing_notice']);
        }
    }

    public function init_components() {
        // Initialize Core Assets & Ajax
        if (class_exists('Palette_Panel_Assets')) Palette_Panel_Assets::get_instance();
        if (class_exists('Palette_Panel_Ajax_Handler')) Palette_Panel_Ajax_Handler::get_instance();

        // Admin Settings
        if (is_admin()) {
            if (class_exists('Palette_Panel_Admin_Settings')) Palette_Panel_Admin_Settings::get_instance();
        }

        // Core Frontend & Modules
        if (class_exists('Palette_Panel_Dashboard')) Palette_Panel_Dashboard::get_instance();
        if (class_exists('Palette_Panel_Auth')) Palette_Panel_Auth::get_instance();
        if (class_exists('Palette_Panel_Menu_Builder')) Palette_Panel_Menu_Builder::get_instance();
        if (class_exists('Palette_Panel_Form_Builder')) Palette_Panel_Form_Builder::get_instance();
        if (class_exists('Palette_Panel_Orders')) Palette_Panel_Orders::get_instance();
        if (class_exists('Palette_Panel_Wallet')) Palette_Panel_Wallet::get_instance();
        if (class_exists('Palette_Panel_Tickets')) Palette_Panel_Tickets::get_instance();
        if (class_exists('Palette_Panel_Live_Chat')) Palette_Panel_Live_Chat::get_instance();
    }

    public function register_panel_rewrite_rules() {
        $opt = get_option('serene_panel_options', []);
        $slug = sanitize_title($opt['panel_slug'] ?? 'panel');
        if (empty($slug)) $slug = 'panel';

        add_rewrite_rule('^' . $slug . '/?$', 'index.php?palette_panel=1', 'top');
        add_rewrite_rule('^' . $slug . '/([^/]+)/?$', 'index.php?palette_panel=1&palette_tab=$matches[1]', 'top');
    }

    public function woocommerce_missing_notice() {
        echo '<div class="notice notice-warning is-dismissible"><p><strong>پالت پنل لایت:</strong> برای استفاده کامل از امکانات فروشگاهی و پیشخوان سفارشات، نصب ووکامرس پیشنهاد می‌شود.</p></div>';
    }
}

function palette_woo_panel_lite() {
    return Palette_Woo_Panel_Lite::instance();
}

palette_woo_panel_lite();

// Explicit Global Shortcode Handlers
add_shortcode('palette_panel', function($atts = []) {
    return Palette_Panel_Dashboard::get_instance()->render_dashboard_shortcode($atts);
});
add_shortcode('palette_dashboard', function($atts = []) {
    return Palette_Panel_Dashboard::get_instance()->render_dashboard_shortcode($atts);
});
add_shortcode('palette_auth', function($atts = []) {
    return Palette_Panel_Dashboard::get_instance()->render_auth_shortcode($atts);
});
add_shortcode('serene_panel', function($atts = []) {
    return Palette_Panel_Dashboard::get_instance()->render_dashboard_shortcode($atts);
});
add_shortcode('serene_dashboard', function($atts = []) {
    return Palette_Panel_Dashboard::get_instance()->render_dashboard_shortcode($atts);
});
add_shortcode('serene_auth', function($atts = []) {
    return Palette_Panel_Dashboard::get_instance()->render_auth_shortcode($atts);
});
