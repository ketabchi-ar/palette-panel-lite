<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Dashboard {
    private static $instance = null;
    private static $rendered_count = 0;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('palette_panel', [$this, 'render_dashboard_shortcode']);
        add_shortcode('palette_dashboard', [$this, 'render_dashboard_shortcode']);
        add_shortcode('palette_auth', [$this, 'render_auth_shortcode']);
        add_shortcode('serene_dashboard', [$this, 'render_dashboard_shortcode']);
        add_shortcode('serene_auth', [$this, 'render_auth_shortcode']);
        add_filter('template_include', [$this, 'handle_panel_page_template'], 99);
    }

    public function handle_panel_page_template($template) {
        // Only load standalone template if explicitly accessing custom rewrite query var
        if (get_query_var('serene-panel')) {
            $custom_template = SERENE_PANEL_TEMPLATES_PATH . 'dashboard.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }

    public function render_dashboard_shortcode($atts) {
        if (self::$rendered_count > 0) {
            return ''; // Prevent double rendering on same request
        }
        self::$rendered_count++;

        Serene_Panel_Assets::get_instance()->enqueue_frontend_assets();

        if (!is_user_logged_in()) {
            return $this->render_auth_form($atts);
        }

        ob_start();
        include SERENE_PANEL_TEMPLATES_PATH . 'dashboard.php';
        return ob_get_clean();
    }

    public function render_auth_shortcode($atts) {
        if (self::$rendered_count > 0) {
            return ''; // Prevent double rendering on same request
        }
        self::$rendered_count++;

        Serene_Panel_Assets::get_instance()->enqueue_frontend_assets();

        if (is_user_logged_in()) {
            ob_start();
            include SERENE_PANEL_TEMPLATES_PATH . 'dashboard.php';
            return ob_get_clean();
        }

        return $this->render_auth_form($atts);
    }

    private function render_auth_form($atts) {
        ob_start();
        include SERENE_PANEL_TEMPLATES_PATH . 'auth/login-page.php';
        return ob_get_clean();
    }

    public static function get_current_tab() {
        $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';
        return $tab;
    }
}
