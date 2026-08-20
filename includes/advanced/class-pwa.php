<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_PWA {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'handle_manifest']);
        add_action('wp_head', [$this, 'render_pwa_tags']);
    }

    public function render_pwa_tags() {
        if (!is_page('panel') && !is_account_page()) return;
        echo '<link rel="manifest" href="' . esc_url(home_url('/?serene_pwa_manifest=1')) . '">';
        echo '<meta name="theme-color" content="#4c5e8b">';
        echo '<meta name="apple-mobile-web-app-capable" content="yes">';
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">';
    }

    public function handle_manifest() {
        if (isset($_GET['serene_pwa_manifest'])) {
            header('Content-Type: application/manifest+json; charset=utf-8');
            echo wp_json_encode([
                'name'             => get_bloginfo('name') . ' - پنل کاربری',
                'short_name'        => get_bloginfo('name'),
                'start_url'        => home_url('/panel/'),
                'display'          => 'standalone',
                'background_color' => '#f9f9fe',
                'theme_color'      => '#4c5e8b',
                'icons'            => [
                    [
                        'src'   => SERENE_PANEL_ASSETS_URL . 'images/icon-192.png',
                        'sizes' => '192x192',
                        'type'  => 'image/png'
                    ],
                    [
                        'src'   => SERENE_PANEL_ASSETS_URL . 'images/icon-512.png',
                        'sizes' => '512x512',
                        'type'  => 'image/png'
                    ]
                ]
            ]);
            exit;
        }
    }
}
