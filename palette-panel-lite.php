<?php
/**
 * Plugin Name: پالت پنل لایت | پنل کاربری رایگان ووکامرس (Palette Panel Lite)
 * Plugin URI: https://palette.agency/
 * Description: نسخه رایگان و سبک پنل کاربری مدرن ووکامرس با دیزاین‌سیستم اختصاصی Serene Professional. برای دسترسی به امکانات پیشرفته (ورود بیومتریک، کارت‌به‌کارت هوشمند، گردونه شانس و کیف پول) نسخه کامل را از ژاکت تهیه فرمایید.
 * Version: 1.0.0
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

define('PALETTE_PANEL_LITE_VERSION', '1.0.0');
define('PALETTE_PANEL_IS_LITE', true);
define('PALETTE_PANEL_VERSION', '1.0.0');
define('SERENE_PANEL_VERSION', '1.0.0');
define('PALETTE_PANEL_FILE', __FILE__);
define('PALETTE_PANEL_PATH', plugin_dir_path(__FILE__));
define('PALETTE_PANEL_URL', plugin_dir_url(__FILE__));
define('PALETTE_PANEL_ASSETS_URL', PALETTE_PANEL_URL . 'assets/');
define('PALETTE_PANEL_TEMPLATES_PATH', PALETTE_PANEL_PATH . 'templates/');

define('SERENE_PANEL_FILE', PALETTE_PANEL_FILE);
define('SERENE_PANEL_PATH', PALETTE_PANEL_PATH);
define('SERENE_PANEL_URL', PALETTE_PANEL_URL);
define('SERENE_PANEL_ASSETS_URL', PALETTE_PANEL_ASSETS_URL);
define('SERENE_PANEL_TEMPLATES_PATH', PALETTE_PANEL_TEMPLATES_PATH);

require_once PALETTE_PANEL_PATH . 'includes/class-autoloader.php';

final class Palette_Woo_Panel_Lite {
    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_autoloader();
        $this->init_hooks();
    }

    private function init_autoloader() {
        Palette_Panel_Autoloader::register();
    }

    private function init_hooks() {
        register_activation_hook(__FILE__, ['Palette_Panel_Activator', 'activate']);
        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    public function init_plugin() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', [$this, 'woocommerce_missing_notice']);
            return;
        }

        // Initialize Core Assets & Ajax
        Palette_Panel_Assets::instance();
        Palette_Panel_Ajax_Handler::instance();

        // Admin & Core Dashboard
        if (is_admin()) {
            Palette_Panel_Admin_Settings::instance();
        }

        Palette_Panel_Dashboard::instance();
        Palette_Panel_Auth_Core::instance();
        Palette_Panel_Menu_Builder::instance();
        Palette_Panel_Form_Builder::instance();
    }

    public function woocommerce_missing_notice() {
        echo '<div class="error"><p><strong>پالت پنل لایت:</strong> برای اجرای این افزونه، نصب و فعال‌سازی فروشگاه‌ساز ووکامرس الزامی است.</p></div>';
    }
}

function palette_woo_panel_lite() {
    return Palette_Woo_Panel_Lite::instance();
}

palette_woo_panel_lite();
