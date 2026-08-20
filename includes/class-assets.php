<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Assets {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets'], 20);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets'], 20);
        add_action('wp_head', [$this, 'inject_custom_theme_variables'], 100);
        add_action('admin_head', [$this, 'inject_custom_theme_variables'], 100);
        add_action('admin_head', [$this, 'inject_admin_theme_skin'], 101);
    }

    public function should_load_assets() {
        if (!is_admin()) {
            return true;
        }
        return false;
    }

    public static function get_theme_colors() {
        $defaults = [
            'primary'           => '#4c5e8b',
            'primary_dim'       => '#40527f',
            'primary_container' => '#b6c8fc',
            'secondary'         => '#585f72',
            'tertiary'          => '#6b5680',
            'surface'           => '#f9f9fe',
            'surface_container' => '#ecedf6',
            'surface_lowest'    => '#ffffff',
            'on_surface'        => '#2f323b',
            'on_surface_variant'=> '#5b5f68',
            'border_radius'     => '1.5rem',
        ];
        
        $opt = get_option('serene_panel_options', []);
        $saved = get_option('serene_panel_theme_colors', []);
        if (!is_array($saved)) $saved = [];

        if (!empty($opt['color_primary'])) {
            $saved['primary'] = $opt['color_primary'];
            $saved['primary_dim'] = $opt['color_primary'];
        }
        if (!empty($opt['color_secondary'])) {
            $saved['secondary'] = $opt['color_secondary'];
        }
        if (!empty($opt['color_tertiary'])) {
            $saved['tertiary'] = $opt['color_tertiary'];
        }

        return wp_parse_args($saved, $defaults);
    }

    public function inject_custom_theme_variables() {
        $colors = self::get_theme_colors();
        echo "<style id='serene-custom-colors'>
        :root {
            --serene-primary: " . esc_attr($colors['primary']) . ";
            --serene-primary-dim: " . esc_attr($colors['primary_dim']) . ";
            --serene-primary-container: " . esc_attr($colors['primary_container']) . ";
            --serene-secondary: " . esc_attr($colors['secondary']) . ";
            --serene-tertiary: " . esc_attr($colors['tertiary']) . ";
            --serene-surface: " . esc_attr($colors['surface']) . ";
            --serene-surface-container: " . esc_attr($colors['surface_container']) . ";
            --serene-surface-container-lowest: " . esc_attr($colors['surface_lowest']) . ";
            --serene-on-surface: " . esc_attr($colors['on_surface']) . ";
            --serene-on-surface-variant: " . esc_attr($colors['on_surface_variant']) . ";
        }
        </style>";
    }

    public function enqueue_frontend_assets() {
        if (!$this->should_load_assets()) {
            return;
        }

        wp_enqueue_style(
            'serene-vazirmatn-font',
            'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;900&display=swap',
            [],
            SERENE_PANEL_VERSION
        );

        wp_enqueue_style(
            'serene-material-symbols',
            'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap',
            [],
            SERENE_PANEL_VERSION
        );

        wp_enqueue_style(
            'serene-material-symbols-cdn',
            'https://cdn.jsdelivr.net/npm/material-symbols@0.14.3/index.min.css',
            [],
            SERENE_PANEL_VERSION
        );

        wp_enqueue_script(
            'serene-tailwind',
            'https://cdn.tailwindcss.com?plugins=forms,container-queries',
            [],
            '3.4.1',
            false
        );

        $colors = self::get_theme_colors();
        $tailwind_config = "
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary': '" . esc_attr($colors['primary']) . "',
                        'primary-dim': '" . esc_attr($colors['primary_dim']) . "',
                        'primary-container': '" . esc_attr($colors['primary_container']) . "',
                        'on-primary-container': '#2e416c',
                        'secondary': '" . esc_attr($colors['secondary']) . "',
                        'secondary-dim': '#4c5366',
                        'secondary-container': '#dbe2f9',
                        'on-secondary-container': '#4a5164',
                        'tertiary': '" . esc_attr($colors['tertiary']) . "',
                        'tertiary-dim': '#5f4b73',
                        'tertiary-container': '#e7cdfe',
                        'on-tertiary-container': '#56426a',
                        'surface': '" . esc_attr($colors['surface']) . "',
                        'surface-bright': '" . esc_attr($colors['surface']) . "',
                        'surface-dim': '#d7dae4',
                        'surface-variant': '#dfe2ed',
                        'surface-container': '" . esc_attr($colors['surface_container']) . "',
                        'surface-container-low': '#f2f3fb',
                        'surface-container-high': '#e6e8f1',
                        'surface-container-highest': '#dfe2ed',
                        'surface-container-lowest': '" . esc_attr($colors['surface_lowest']) . "',
                        'on-surface': '" . esc_attr($colors['on_surface']) . "',
                        'on-surface-variant': '" . esc_attr($colors['on_surface_variant']) . "',
                        'outline': '#777b84',
                        'outline-variant': '#dfe2ed',
                        'error': '#a83836',
                        'error-dim': '#67040d',
                        'error-container': '#fa746f',
                        'on-error-container': '#6e0a12',
                        'background': '" . esc_attr($colors['surface']) . "',
                        'on-background': '" . esc_attr($colors['on_surface']) . "'
                    },
                    fontFamily: {
                        body: ['Vazirmatn', 'Tahoma', 'sans-serif'],
                        headline: ['Vazirmatn', 'Tahoma', 'sans-serif']
                    }
                }
            }
        };
        ";
        wp_add_inline_script('serene-tailwind', $tailwind_config, 'after');

        wp_enqueue_style(
            'serene-panel-css',
            SERENE_PANEL_ASSETS_URL . 'css/serene-panel.css',
            [],
            SERENE_PANEL_VERSION
        );

        wp_enqueue_script(
            'serene-panel-js',
            SERENE_PANEL_ASSETS_URL . 'js/serene-panel.js',
            ['jquery'],
            SERENE_PANEL_VERSION,
            true
        );

        wp_enqueue_script(
            'serene-auth-js',
            SERENE_PANEL_ASSETS_URL . 'js/serene-auth.js',
            ['serene-panel-js'],
            SERENE_PANEL_VERSION,
            true
        );

        $current_uid = is_user_logged_in() ? get_current_user_id() : 0;
        $options = get_option('serene_panel_options', []);

        if (!empty($options['enable_google_auth']) && !empty($options['google_client_id'])) {
            wp_enqueue_script('google-gsi-client', 'https://accounts.google.com/gsi/client', [], null, true);
        }

        wp_localize_script('serene-panel-js', 'sereneConfig', [
            'ajax_url'            => admin_url('admin-ajax.php'),
            'rest_url'            => esc_url_raw(rest_url('serene/v1/')),
            'nonce'               => wp_create_nonce('serene_panel_nonce'),
            'is_logged_in'        => is_user_logged_in() ? 1 : 0,
            'current_user'        => $current_uid,
            'home_url'            => home_url('/'),
            'panel_url'           => home_url('/panel/'),
            'lucky_wheel_slices'  => Serene_Panel_Lucky_Wheel::get_wheel_slices(),
            'can_spin_wheel'      => $current_uid ? Serene_Panel_Lucky_Wheel::can_user_spin($current_uid) : false,
            'auth_options'        => [
                'enable_google_auth'   => !empty($options['enable_google_auth']) && !empty($options['google_client_id']),
                'google_client_id'     => $options['google_client_id'] ?? '',
                'enable_bale_auth'     => !empty($options['enable_bale_auth']),
                'bale_bot_username'    => $options['bale_bot_username'] ?? '',
                'enable_telegram_auth' => !empty($options['enable_telegram_auth']),
                'telegram_bot_username'=> $options['telegram_bot_username'] ?? '',
                'enable_voice_auth'    => !empty($options['enable_voice_auth']),
                'enable_biometric'     => !empty($options['enable_biometric']),
                'enable_2fa'           => !empty($options['enable_2fa']),
            ],
            'i18n'                => [
                'sending'        => 'در حال ارسال...',
                'sent_success'   => 'کد تایید با موفقیت ارسال شد.',
                'verify_success' => 'ورود با موفقیت انجام شد.',
                'error_generic'  => 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.',
                'spin_win'       => 'تبریک! شما برنده شدید:',
            ]
        ]);
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'palette-panel') === false && strpos($hook, 'serene-panel') === false && strpos($hook, 'palette-sms') === false && strpos($hook, 'palette-tickets') === false) return;

        wp_enqueue_media();
        wp_enqueue_style('serene-vazirmatn-font', 'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap');
        wp_enqueue_style('serene-material-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');
        wp_enqueue_script('serene-tailwind', 'https://cdn.tailwindcss.com?plugins=forms,container-queries', [], '3.4.1', false);
        
        $colors = self::get_theme_colors();
        $tailwind_config = "
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary': '" . esc_attr($colors['primary']) . "',
                        'primary-dim': '" . esc_attr($colors['primary_dim']) . "',
                        'primary-container': '" . esc_attr($colors['primary_container']) . "',
                        'on-primary-container': '#2e416c',
                        'secondary': '" . esc_attr($colors['secondary']) . "',
                        'secondary-dim': '#4c5366',
                        'secondary-container': '#dbe2f9',
                        'on-secondary-container': '#4a5164',
                        'tertiary': '" . esc_attr($colors['tertiary']) . "',
                        'tertiary-dim': '#5f4b73',
                        'tertiary-container': '#e7cdfe',
                        'on-tertiary-container': '#56426a',
                        'surface': '" . esc_attr($colors['surface']) . "',
                        'surface-bright': '" . esc_attr($colors['surface']) . "',
                        'surface-dim': '#d7dae4',
                        'surface-variant': '#dfe2ed',
                        'surface-container': '" . esc_attr($colors['surface_container']) . "',
                        'surface-container-low': '#f2f3fb',
                        'surface-container-high': '#e6e8f1',
                        'surface-container-highest': '#dfe2ed',
                        'surface-container-lowest': '" . esc_attr($colors['surface_lowest']) . "',
                        'on-surface': '" . esc_attr($colors['on_surface']) . "',
                        'on-surface-variant': '" . esc_attr($colors['on_surface_variant']) . "',
                        'outline': '#777b84',
                        'outline-variant': '#dfe2ed',
                        'error': '#a83836',
                        'error-dim': '#67040d',
                        'error-container': '#fa746f',
                        'on-error-container': '#6e0a12',
                        'background': '" . esc_attr($colors['surface']) . "',
                        'on-background': '" . esc_attr($colors['on_surface']) . "'
                    }
                }
            }
        };
        window.sereneConfig = {
            ajax_url: '" . admin_url('admin-ajax.php') . "',
            nonce: '" . wp_create_nonce('serene_panel_nonce') . "'
        };
        window.sereneAdminConfig = window.sereneConfig;
        ";
        wp_add_inline_script('serene-tailwind', $tailwind_config, 'after');

        wp_enqueue_style('serene-admin-css', SERENE_PANEL_ASSETS_URL . 'css/serene-panel.css', [], SERENE_PANEL_VERSION);

        $opt = get_option('serene_panel_options', []);
        $input_style = $opt['input_field_style'] ?? 'soft';
        $admin_soft_ui = self::get_input_style_css($input_style);
        wp_add_inline_style('serene-admin-css', $admin_soft_ui);
    }

    public static function get_input_style_css($style = 'soft') {
        $colors = self::get_theme_colors();
        $primary = $colors['primary'] ?? '#4c5e8b';

        $css = "
        :root {
            --serene-primary: {$primary};
        }

        /* Universal Typography & Placeholder Normalization */
        #serene-admin-app,
        #serene-admin-app input,
        #serene-admin-app select,
        #serene-admin-app textarea,
        #serene-admin-app table,
        #serene-admin-app th,
        #serene-admin-app td,
        .serene-panel,
        .serene-input {
            font-family: 'Vazirmatn', Tahoma, -apple-system, BlinkMacSystemFont, sans-serif !important;
        }

        /* Protect Material Symbols from font overrides */
        #serene-admin-app .material-symbols-outlined,
        .material-symbols-outlined,
        .material-symbols-outlined * {
            font-family: 'Material Symbols Outlined' !important;
            font-weight: normal !important;
            font-style: normal !important;
            font-size: 20px;
            line-height: 1 !important;
            display: inline-block !important;
            vertical-align: middle !important;
            letter-spacing: normal !important;
            text-transform: none !important;
            white-space: nowrap !important;
            word-wrap: normal !important;
            direction: ltr !important;
            -webkit-font-feature-settings: 'liga' !important;
            -webkit-font-smoothing: antialiased !important;
        }

        #serene-admin-app input::placeholder,
        #serene-admin-app textarea::placeholder,
        .serene-input::placeholder,
        input::placeholder,
        textarea::placeholder {
            font-family: 'Vazirmatn', Tahoma, sans-serif !important;
            font-weight: 400 !important;
            color: #94a3b8 !important;
            opacity: 0.85 !important;
        }

        #serene-admin-app input[dir='ltr']::placeholder,
        #serene-admin-app input.font-mono::placeholder,
        #serene-admin-app input[type='tel']::placeholder,
        #serene-admin-app input[type='number']::placeholder {
            font-family: 'Vazirmatn', Tahoma, sans-serif !important;
        }

        /* Select Dropdown Squashing & Text Baseline Fix */
        #serene-admin-app select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E\") !important;
            background-repeat: no-repeat !important;
            background-position: left 0.85rem center !important;
            background-size: 1rem 1rem !important;
            padding-left: 2.25rem !important;
            padding-right: 0.95rem !important;
            padding-top: 0.55rem !important;
            padding-bottom: 0.55rem !important;
            line-height: 1.5 !important;
            min-height: 42px !important;
            height: auto !important;
            vertical-align: middle !important;
            cursor: pointer !important;
        }
        ";

        if ($style === 'pill') {
            // Model 1: Pill & Ultra-Soft Rounded (کپسولی و فوق‌العاده نرم)
            $css .= "
            #serene-admin-app input[type='text'],
            #serene-admin-app input[type='number'],
            #serene-admin-app input[type='password'],
            #serene-admin-app input[type='tel'],
            #serene-admin-app input[type='email'],
            #serene-admin-app input[type='search'],
            #serene-admin-app select,
            .serene-input {
                border-radius: 9999px !important;
                border: 1.5px solid rgba(203, 213, 225, 0.8) !important;
                background-color: #f8fafc !important;
                padding: 0.75rem 1.35rem !important;
                font-size: 0.8125rem !important;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
            }
            #serene-admin-app textarea {
                border-radius: 1.5rem !important;
                border: 1.5px solid rgba(203, 213, 225, 0.8) !important;
                background-color: #f8fafc !important;
                padding: 0.85rem 1.25rem !important;
                font-size: 0.8125rem !important;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
            }
            #serene-admin-app input:focus,
            #serene-admin-app select:focus,
            #serene-admin-app textarea:focus,
            .serene-input:focus {
                background-color: #ffffff !important;
                border-color: {$primary} !important;
                box-shadow: 0 0 0 4px rgba(76, 94, 139, 0.15) !important;
                outline: none !important;
            }
            ";
        } elseif ($style === 'glass') {
            // Model 3: Frosted Glass & Neu-Soft (شیشه‌ای نئومورفیک و مات)
            $css .= "
            #serene-admin-app input[type='text'],
            #serene-admin-app input[type='number'],
            #serene-admin-app input[type='password'],
            #serene-admin-app input[type='tel'],
            #serene-admin-app input[type='email'],
            #serene-admin-app input[type='search'],
            #serene-admin-app select,
            #serene-admin-app textarea,
            .serene-input {
                border-radius: 0.875rem !important;
                border: 1.5px solid rgba(203, 213, 225, 0.9) !important;
                background: rgba(248, 250, 252, 0.75) !important;
                backdrop-filter: blur(8px) !important;
                padding: 0.65rem 0.95rem !important;
                font-size: 0.8125rem !important;
                box-shadow: inset 0 2px 4px rgba(0,0,0,0.03) !important;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            #serene-admin-app input:focus,
            #serene-admin-app select:focus,
            #serene-admin-app textarea:focus,
            .serene-input:focus {
                background: #ffffff !important;
                border-color: {$primary} !important;
                box-shadow: 0 0 0 4px rgba(76, 94, 139, 0.16), inset 0 1px 2px rgba(0,0,0,0.02) !important;
                outline: none !important;
            }
            ";
        } else {
            // Model 2 (Default): Modern Soft 2XL (ارگونومیک مدرن و لطیف)
            $css .= "
            #serene-admin-app input[type='text'],
            #serene-admin-app input[type='number'],
            #serene-admin-app input[type='password'],
            #serene-admin-app input[type='tel'],
            #serene-admin-app input[type='email'],
            #serene-admin-app input[type='search'],
            #serene-admin-app select,
            #serene-admin-app textarea,
            .serene-input {
                border-radius: 1rem !important;
                border: 1.5px solid rgba(203, 213, 225, 0.75) !important;
                background-color: #f8fafc !important;
                padding: 0.65rem 0.95rem !important;
                font-size: 0.8125rem !important;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            }
            #serene-admin-app input:focus,
            #serene-admin-app select:focus,
            #serene-admin-app textarea:focus,
            .serene-input:focus {
                background-color: #ffffff !important;
                border-color: {$primary} !important;
                box-shadow: 0 0 0 4px rgba(76, 94, 139, 0.12) !important;
                outline: none !important;
            }
            ";
        }

        $css .= "
        #serene-admin-app input[type='checkbox']:not(.sr-only) {
            border-radius: 0.5rem !important;
            border: 1.5px solid #cbd5e1 !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            accent-color: {$primary} !important;
            cursor: pointer !important;
            transition: all 0.15s ease-in-out !important;
        }
        #serene-admin-app input[type='color'] {
            border-radius: 0.75rem !important;
            border: 2px solid #e2e8f0 !important;
            overflow: hidden !important;
            cursor: pointer !important;
            padding: 0 !important;
        }
        ";

        return $css;
    }

    public function inject_admin_theme_skin() {
        $opt = get_option('serene_panel_options', []);
        $input_style = $opt['input_field_style'] ?? 'soft';
        echo "<style id='palette-input-styles'>" . self::get_input_style_css($input_style) . "</style>";

        $theme = $opt['admin_dashboard_theme'] ?? 'wp_default';
        if ($theme === 'wp_default') return;

        $sidebar_bg = !empty($opt['admin_custom_sidebar_bg']) ? $opt['admin_custom_sidebar_bg'] : ($theme === 'clean_saas' ? '#f8fafc' : ($theme === 'midnight_dark' ? '#0b0f19' : '#1e293b'));
        $accent_col = !empty($opt['admin_custom_accent']) ? $opt['admin_custom_accent'] : ($theme === 'clean_saas' ? '#4c5e8b' : ($theme === 'midnight_dark' ? '#2563eb' : '#3b82f6'));
        $topbar_bg  = !empty($opt['admin_custom_topbar_bg']) ? $opt['admin_custom_topbar_bg'] : ($theme === 'clean_saas' ? '#ffffff' : ($theme === 'midnight_dark' ? '#0b0f19' : '#0f172a'));

        $is_light_saas = ($theme === 'clean_saas');
        $text_color = $is_light_saas ? '#475569' : '#cbd5e1';
        $sub_text_col = $is_light_saas ? '#334155' : '#fff';
        $topbar_text = $is_light_saas ? '#334155' : '#ffffff';
        $submenu_bg = $is_light_saas ? '#ffffff' : ($theme === 'midnight_dark' ? '#131b2e' : '#0f172a');

        echo "<style id='palette-admin-theme'>
        body, #wpadminbar, #adminmenu, #adminmenuback, #adminmenuwrap { font-family: 'Vazirmatn', Tahoma, sans-serif !important; }
        #adminmenuback, #adminmenuwrap, #adminmenu { background-color: " . esc_attr($sidebar_bg) . " !important; " . ($is_light_saas ? "border-left: 1px solid #e2e8f0;" : "") . " }
        #adminmenu a { color: " . esc_attr($text_color) . " !important; font-weight: 500; }
        #adminmenu li.menu-top:hover, #adminmenu li.opensub>a.menu-top, #adminmenu li>a.menu-top:focus { background-color: rgba(255,255,255,0.08) !important; color: " . esc_attr($sub_text_col) . " !important; }
        #adminmenu li.current a.menu-top { background: " . esc_attr($accent_col) . " !important; color: #fff !important; }
        #adminmenu .wp-submenu { background: " . esc_attr($submenu_bg) . " !important; " . ($is_light_saas ? "box-shadow: 0 10px 25px rgba(0,0,0,0.08); border-radius: 12px;" : "") . " }
        #adminmenu .wp-submenu a { color: " . esc_attr($text_color) . " !important; }
        #adminmenu .wp-submenu a:hover { color: " . esc_attr($accent_col) . " !important; }
        #wpadminbar { background: " . esc_attr($topbar_bg) . " !important; " . ($is_light_saas ? "border-bottom: 1px solid #e2e8f0;" : "") . " }
        #wpadminbar .ab-item, #wpadminbar a.ab-item, #wpadminbar > #wp-toolbar span.ab-label { color: " . esc_attr($topbar_text) . " !important; }
        .wp-core-ui .button-primary { background: " . esc_attr($accent_col) . " !important; border-color: " . esc_attr($accent_col) . " !important; border-radius: 8px !important; }
        </style>";
    }
}
