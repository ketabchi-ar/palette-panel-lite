<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Admin_Settings {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_filter('admin_footer_text', [$this, 'customize_admin_footer_text'], 99);
        add_filter('update_footer', [$this, 'customize_admin_update_footer'], 99);
    }

    public function register_admin_menu() {
        add_menu_page(
            'تنظیمات پالت پنل',
            'پالت پنل 🎨',
            'manage_options',
            'palette-panel-settings',
            [$this, 'render_settings_page'],
            'dashicons-art',
            56
        );

        add_submenu_page(
            'palette-panel-settings',
            'مرکز تنظیمات پالت پنل',
            'تنظیمات و ماژول‌ها',
            'manage_options',
            'palette-panel-settings',
            [$this, 'render_settings_page']
        );

        $pending_count = class_exists('Serene_Panel_Tickets') ? Serene_Panel_Tickets::get_pending_tickets_count() : 0;
        $badge = $pending_count > 0 ? sprintf(' <span class="update-plugins count-%d"><span class="plugin-count">%d</span></span>', $pending_count, $pending_count) : '';

        add_submenu_page(
            'palette-panel-settings',
            'مدیریت تیکت‌ها و پشتیبانی',
            'مدیریت تیکت‌ها' . $badge,
            'manage_options',
            'palette-tickets',
            [$this, 'render_tickets_page']
        );

        add_submenu_page(
            'palette-panel-settings',
            'دیباگر و لاگ پیامک‌ها',
            'لاگ پیامک‌ها',
            'manage_options',
            'palette-sms-logs',
            [$this, 'render_sms_logs_page']
        );
    }

    public function render_settings_page() {
        $options = get_option('serene_panel_options', []);
        include SERENE_PANEL_PATH . 'includes/admin/views/settings-page.php';
    }

    public function render_tickets_page() {
        include SERENE_PANEL_PATH . 'includes/admin/views/tickets-page.php';
    }

    public function is_palette_panel_screen() {
        $screen = get_current_screen();
        if (!$screen) return false;
        $allowed_screens = [
            'toplevel_page_palette-panel-settings',
            'palette-panel-settings_page_palette-tickets',
            'palette-panel-settings_page_palette-sms-logs',
            'palette-panel_page_palette-panel-settings',
            'palette-panel_page_palette-tickets',
            'palette-panel_page_palette-sms-logs',
        ];
        $is_page = isset($_GET['page']) && in_array($_GET['page'], ['palette-panel-settings', 'palette-tickets', 'palette-sms-logs'], true);
        return in_array($screen->id, $allowed_screens, true) || $is_page;
    }

    public function customize_admin_footer_text($footer_text) {
        if ($this->is_palette_panel_screen()) {
            return '<span class="palette-footer-text font-body text-xs text-slate-500">توسعه یافته با ❤️ توسط <a href="https://palette.agency/" target="_blank" class="font-bold text-primary hover:underline">آژانس دیجیتال پالت</a></span>';
        }
        return $footer_text;
    }

    public function customize_admin_update_footer($version_text) {
        if ($this->is_palette_panel_screen()) {
            $ver = defined('PALETTE_PANEL_VERSION') ? PALETTE_PANEL_VERSION : '1.3.0';
            return '<span class="palette-version-text text-xs text-slate-400 font-mono">پالت پنل نسخه ' . esc_html($ver) . '</span>';
        }
        return $version_text;
    }

    public function render_sms_logs_page() {
        $logs = Serene_Panel_SMS_Logger::get_recent_logs(100);
        ?>
        <div class="wrap" dir="rtl">
            <h1 class="wp-heading-inline">📊 دیباگر و تاریخچه ارسال پیامک‌ها</h1>
            <table class="wp-list-table widefat fixed striped table-view-list" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>شماره گیرنده</th>
                        <th>سامانه</th>
                        <th>متن / کد پترن</th>
                        <th>وضعیت</th>
                        <th>پاسخ وب‌سرویس</th>
                        <th>آدرس IP</th>
                        <th>تاریخ ارسال</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                    <tr><td colspan="8" style="text-align:center; padding: 20px;">هیچ لاگی ثبت نشده است.</td></tr>
                    <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo esc_html($log->id); ?></td>
                        <td><strong><?php echo esc_html($log->phone); ?></strong></td>
                        <td><span class="badge"><?php echo esc_html($log->provider); ?></span></td>
                        <td><?php echo esc_html($log->message); ?></td>
                        <td>
                            <?php if ($log->status === 'sent'): ?>
                                <span style="color: green; font-weight: bold;">✓ موفق</span>
                            <?php else: ?>
                                <span style="color: red; font-weight: bold;">✗ ناموفق</span>
                            <?php endif; ?>
                        </td>
                        <td><code><?php echo esc_html($log->response); ?></code></td>
                        <td><?php echo esc_html($log->ip_address); ?></td>
                        <td><?php echo esc_html($log->created_at); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

class_alias('Serene_Panel_Admin_Settings', 'Palette_Panel_Admin_Settings');
