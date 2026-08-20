<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Activator {
    public static function activate() {
        self::create_tables();
        self::set_default_options();
        flush_rewrite_rules();
    }

    public static function deactivation_cleanup() {
        flush_rewrite_rules();
    }

    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // 1. SMS Logs Table
        $table_sms_logs = $wpdb->prefix . 'serene_sms_logs';
        $sql_sms = "CREATE TABLE $table_sms_logs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            phone varchar(20) NOT NULL,
            provider varchar(50) NOT NULL,
            message text NOT NULL,
            pattern_code varchar(50) DEFAULT '',
            status varchar(20) NOT NULL DEFAULT 'pending',
            response text DEFAULT NULL,
            ip_address varchar(45) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY phone_idx (phone),
            KEY status_idx (status)
        ) $charset_collate;";
        dbDelta($sql_sms);

        // 2. Wallet Transactions Table
        $table_wallet = $wpdb->prefix . 'serene_wallet_tx';
        $sql_wallet = "CREATE TABLE $table_wallet (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            type varchar(20) NOT NULL,
            amount decimal(15,2) NOT NULL,
            balance_after decimal(15,2) NOT NULL DEFAULT 0.00,
            description varchar(255) NOT NULL,
            order_id bigint(20) unsigned DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_idx (user_id)
        ) $charset_collate;";
        dbDelta($sql_wallet);

        // 3. Support Tickets Table
        $table_tickets = $wpdb->prefix . 'serene_tickets';
        $sql_tickets = "CREATE TABLE $table_tickets (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            department varchar(50) NOT NULL DEFAULT 'support',
            priority varchar(20) NOT NULL DEFAULT 'medium',
            subject varchar(255) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'open',
            last_reply_by varchar(20) DEFAULT 'user',
            updated_at datetime DEFAULT CURRENT_TIMESTAMP,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_idx (user_id),
            KEY status_idx (status)
        ) $charset_collate;";
        dbDelta($sql_tickets);

        // 4. Ticket Messages Table
        $table_ticket_msgs = $wpdb->prefix . 'serene_ticket_messages';
        $sql_ticket_msgs = "CREATE TABLE $table_ticket_msgs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            ticket_id bigint(20) unsigned NOT NULL,
            sender_id bigint(20) unsigned NOT NULL,
            is_admin tinyint(1) NOT NULL DEFAULT 0,
            message text NOT NULL,
            attachment_url text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY ticket_idx (ticket_id)
        ) $charset_collate;";
        dbDelta($sql_ticket_msgs);

        // 5. Card-to-Card Automated Transfers Table
        $table_c2c = $wpdb->prefix . 'serene_card_transfers';
        $sql_c2c = "CREATE TABLE $table_c2c (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            order_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            card_number varchar(30) DEFAULT '',
            bank_name varchar(50) DEFAULT '',
            tracking_code varchar(100) NOT NULL,
            amount decimal(15,2) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            receipt_image text DEFAULT NULL,
            sms_raw text DEFAULT NULL,
            matched_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY order_idx (order_id),
            KEY track_idx (tracking_code)
        ) $charset_collate;";
        dbDelta($sql_c2c);

        // 6. Targeted Notifications Table
        $table_notifs = $wpdb->prefix . 'serene_notifications';
        $sql_notifs = "CREATE TABLE $table_notifs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL DEFAULT 0,
            title varchar(255) NOT NULL,
            message text NOT NULL,
            type varchar(20) NOT NULL DEFAULT 'info',
            is_read tinyint(1) NOT NULL DEFAULT 0,
            target_roles varchar(255) DEFAULT 'all',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_idx (user_id)
        ) $charset_collate;";
        dbDelta($sql_notifs);

        // 7. Lucky Wheel Spins Table
        $table_wheel = $wpdb->prefix . 'serene_lucky_wheel_spins';
        $sql_wheel = "CREATE TABLE $table_wheel (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            prize_type varchar(50) NOT NULL,
            prize_value varchar(255) NOT NULL,
            coupon_code varchar(100) DEFAULT '',
            expires_at datetime DEFAULT NULL,
            is_claimed tinyint(1) NOT NULL DEFAULT 0,
            ip_address varchar(45) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_idx (user_id)
        ) $charset_collate;";
        dbDelta($sql_wheel);

        // 8. Active Sessions Table
        $table_sessions = $wpdb->prefix . 'serene_active_sessions';
        $sql_sessions = "CREATE TABLE $table_sessions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            session_token varchar(64) NOT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent varchar(255) NOT NULL,
            device_name varchar(100) DEFAULT 'مرورگر دسکتاپ',
            last_activity datetime DEFAULT CURRENT_TIMESTAMP,
            is_revoked tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY token_unique (session_token),
            KEY user_idx (user_id)
        ) $charset_collate;";
        dbDelta($sql_sessions);

        // 9. RMA / Warranty Claims Table
        $table_rma = $wpdb->prefix . 'serene_rma_requests';
        $sql_rma = "CREATE TABLE $table_rma (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            order_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            product_id bigint(20) unsigned NOT NULL,
            reason text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            images text DEFAULT NULL,
            admin_note text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_idx (user_id),
            KEY order_idx (order_id)
        ) $charset_collate;";
        dbDelta($sql_rma);

        // 10. Price & Stock Alerts Table
        $table_alerts = $wpdb->prefix . 'serene_price_alerts';
        $sql_alerts = "CREATE TABLE $table_alerts (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            product_id bigint(20) unsigned NOT NULL,
            target_price decimal(15,2) DEFAULT 0.00,
            alert_type varchar(20) NOT NULL DEFAULT 'price_drop',
            is_notified tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_prod_idx (user_id, product_id)
        ) $charset_collate;";
        dbDelta($sql_alerts);

        // 11. System Logs & Debugger Table
        $table_sys_logs = $wpdb->prefix . 'serene_system_logs';
        $sql_sys = "CREATE TABLE $table_sys_logs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            level varchar(20) NOT NULL DEFAULT 'INFO',
            channel varchar(50) NOT NULL DEFAULT 'SYSTEM',
            message text NOT NULL,
            context longtext DEFAULT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            ip_address varchar(45) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY level_idx (level),
            KEY chan_idx (channel),
            KEY date_idx (created_at)
        ) $charset_collate;";
        dbDelta($sql_sys);
    }

    public static function set_default_options() {
        if (!get_option('serene_panel_options')) {
            $defaults = [
                'sms_provider'          => 'kavenegar',
                'sms_api_key'           => '',
                'sms_sender'            => '',
                'sms_otp_pattern'       => '',
                'otp_length'            => 5,
                'otp_expiry'            => 120,
                'enable_sms_auth'       => 1,
                'enable_email_auth'     => 1,
                'enable_voice_auth'     => 1,
                'enable_bale_auth'      => 0,
                'enable_telegram_auth'  => 0,
                'enable_google_auth'    => 0,
                'enable_biometric'      => 1,
                'enable_2fa'            => 1,
                'disable_admin_sms'     => 1,
                'rate_limit_per_minute' => 3,
                'ip_ban_threshold'      => 5,
                'enable_quick_checkout' => 1,
                'enable_wallet'         => 1,
                'cashback_percent'      => 5,
                'enable_c2c'            => 1,
                'c2c_card_number'       => '6037997100000000',
                'c2c_card_holder'       => 'مدیریت فروشگاه',
                'c2c_bank_name'         => 'ملی',
                'c2c_webhook_secret'    => wp_generate_password(24, false),
                'enable_tickets'        => 1,
                'enable_live_chat'      => 1,
                'enable_lucky_wheel'    => 1,
                'lucky_wheel_daily_limit'=> 1,
                'enable_smart_reviews'  => 1,
                'review_reward_amount'  => 10000,
                'enable_pwa'            => 1,
                'enable_shahkar'        => 0,
                'enable_affiliate'      => 1,
                'affiliate_commission'  => 10,
                'enable_post_tracking'  => 1,
                'enable_pdf_invoice'    => 1,
                'enable_rma'            => 1,
                'enable_loyalty_tiers'  => 1,
                'enable_price_alerts'   => 1,
                'enable_active_sessions'=> 1,
                'enable_license_manager'=> 1,
            ];
            update_option('serene_panel_options', $defaults);
        }
    }

    public static function check_db_migrations() {
        global $wpdb;
        $table_wheel = $wpdb->prefix . 'serene_lucky_wheel_spins';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_wheel}'") === $table_wheel) {
            $col_expires = $wpdb->get_results("SHOW COLUMNS FROM `{$table_wheel}` LIKE 'expires_at'");
            if (empty($col_expires)) {
                $wpdb->query("ALTER TABLE `{$table_wheel}` ADD COLUMN `expires_at` datetime DEFAULT NULL AFTER `coupon_code`");
            }
            $col_claimed = $wpdb->get_results("SHOW COLUMNS FROM `{$table_wheel}` LIKE 'is_claimed'");
            if (empty($col_claimed)) {
                $wpdb->query("ALTER TABLE `{$table_wheel}` ADD COLUMN `is_claimed` tinyint(1) NOT NULL DEFAULT 0 AFTER `expires_at`");
            }
        }

        // Check if ticket tables exist
        $table_tickets = $wpdb->prefix . 'serene_tickets';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_tickets}'") !== $table_tickets) {
            self::create_tables();
        }
    }
}
