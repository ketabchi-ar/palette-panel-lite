<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Ajax_Handler {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Auth actions
        $this->add_ajax('serene_send_otp', [$this, 'ajax_send_otp'], true);
        $this->add_ajax('serene_verify_otp', [$this, 'ajax_verify_otp'], true);
        $this->add_ajax('palette_login_with_password', [$this, 'ajax_login_with_password'], true);
        $this->add_ajax('serene_login_with_password', [$this, 'ajax_login_with_password'], true);
        $this->add_ajax('serene_webauthn_register_options', [$this, 'ajax_webauthn_reg_options']);
        $this->add_ajax('serene_webauthn_register_save', [$this, 'ajax_webauthn_reg_save']);
        $this->add_ajax('serene_webauthn_login_options', [$this, 'ajax_webauthn_login_options'], true);
        $this->add_ajax('serene_webauthn_login_verify', [$this, 'ajax_webauthn_login_verify'], true);
        $this->add_ajax('serene_2fa_setup', [$this, 'ajax_2fa_setup']);
        $this->add_ajax('serene_2fa_verify', [$this, 'ajax_2fa_verify'], true);
        $this->add_ajax('serene_2fa_disable', [$this, 'ajax_2fa_disable']);
        $this->add_ajax('serene_webauthn_delete_key', [$this, 'ajax_webauthn_delete_key']);
        $this->add_ajax('serene_google_login', [$this, 'ajax_google_login'], true);

        // Dashboard & Profile
        $this->add_ajax('serene_update_profile', [$this, 'ajax_update_profile']);
        $this->add_ajax('serene_upload_avatar', [$this, 'ajax_upload_avatar']);

        // Commerce & Fintech
        $this->add_ajax('serene_quick_checkout', [$this, 'ajax_quick_checkout'], true);
        $this->add_ajax('serene_c2c_submit', [$this, 'ajax_c2c_submit'], true);
        $this->add_ajax('serene_submit_c2c_receipt', [$this, 'ajax_c2c_submit'], true);
        $this->add_ajax('serene_wallet_transfer', [$this, 'ajax_wallet_transfer']);

        // Engagement & Support
        $this->add_ajax('serene_create_ticket', [$this, 'ajax_create_ticket']);
        $this->add_ajax('serene_reply_ticket', [$this, 'ajax_reply_ticket']);
        $this->add_ajax('serene_spin_wheel', [$this, 'ajax_spin_wheel']);
        $this->add_ajax('serene_submit_review', [$this, 'ajax_submit_review']);

        // Advanced
        $this->add_ajax('serene_revoke_session', [$this, 'ajax_revoke_session']);
        $this->add_ajax('serene_revoke_all_sessions', [$this, 'ajax_revoke_all_sessions']);
        $this->add_ajax('serene_create_rma', [$this, 'ajax_create_rma']);
        $this->add_ajax('serene_subscribe_price_alert', [$this, 'ajax_subscribe_price_alert'], true);
        $this->add_ajax('palette_subscribe_price_alert', [$this, 'ajax_subscribe_price_alert'], true);

        // Admin Actions
        $this->add_ajax('serene_admin_save_settings', [$this, 'ajax_admin_save_settings']);
        $this->add_ajax('palette_admin_save_settings', [$this, 'ajax_admin_save_settings']);
        $this->add_ajax('serene_admin_test_sms', [$this, 'ajax_admin_test_sms']);
        $this->add_ajax('serene_admin_clear_logs', [$this, 'ajax_admin_clear_logs']);
        $this->add_ajax('serene_admin_get_logs', [$this, 'ajax_admin_get_logs']);
        $this->add_ajax('serene_admin_search_users', [$this, 'ajax_admin_search_users']);
        $this->add_ajax('serene_admin_adjust_wallet', [$this, 'ajax_admin_adjust_wallet']);
        $this->add_ajax('serene_admin_get_ticket_details', [$this, 'ajax_admin_get_ticket_details']);
        $this->add_ajax('serene_admin_reply_ticket', [$this, 'ajax_admin_reply_ticket']);
        $this->add_ajax('serene_admin_update_ticket_status', [$this, 'ajax_admin_update_ticket_status']);
        $this->add_ajax('serene_admin_update_ticket_priority', [$this, 'ajax_admin_update_ticket_priority']);
        $this->add_ajax('serene_admin_delete_ticket', [$this, 'ajax_admin_delete_ticket']);
        $this->add_ajax('serene_admin_save_departments', [$this, 'ajax_admin_save_departments']);
        $this->add_ajax('serene_admin_verify_c2c_receipt', [$this, 'ajax_admin_verify_c2c_receipt']);
        $this->add_ajax('serene_admin_review_c2c', [$this, 'ajax_admin_verify_c2c_receipt']);
        $this->add_ajax('serene_admin_learn_bank_pattern', [$this, 'ajax_admin_learn_bank_pattern']);
        $this->add_ajax('serene_admin_delete_bank_pattern', [$this, 'ajax_admin_delete_bank_pattern']);
        $this->add_ajax('serene_admin_bulk_adjust_wallet', [$this, 'ajax_admin_bulk_adjust_wallet']);
        $this->add_ajax('serene_admin_save_carriers', [$this, 'ajax_admin_save_carriers']);
        $this->add_ajax('serene_admin_save_loyalty_tiers', [$this, 'ajax_admin_save_loyalty_tiers']);
        $this->add_ajax('palette_admin_save_loyalty_tiers', [$this, 'ajax_admin_save_loyalty_tiers']);
        $this->add_ajax('serene_admin_delete_wheel_coupon', [$this, 'ajax_admin_delete_wheel_coupon']);
        $this->add_ajax('serene_admin_clear_expired_wheel_coupons', [$this, 'ajax_admin_clear_expired_wheel_coupons']);
    }

    private function add_ajax($action, $callback, $nopriv = false) {
        add_action('wp_ajax_' . $action, $callback);
        if ($nopriv) {
            add_action('wp_ajax_nopriv_' . $action, $callback);
        }
    }

    // 1. Send OTP
    public function ajax_send_otp() {
        Serene_Panel_Security::verify_nonce();

        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $type  = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'sms';
        $method= isset($_POST['method']) ? sanitize_key($_POST['method']) : 'text';

        if ($type === 'sms' || $type === 'voice') {
            if (!Serene_Panel_Auth::is_valid_iran_phone($phone)) {
                wp_send_json_error(['message' => 'شماره موبایل وارد شده نامعتبر است. (مثال: 09123456789)']);
            }
            $phone = Serene_Panel_Auth::normalize_phone($phone);

            // Check sensitive role check
            if (!apply_filters('serene_panel_can_send_otp', true, $phone)) {
                wp_send_json_error(['message' => 'ورود با پیامک برای این حساب کاربری غیرفعال است. لطفاً از رمز عبور استفاده کنید.']);
            }

            // Rate Limit
            $rl = Serene_Panel_Rate_Limiter::check_sms_rate_limit($phone);
            if (is_wp_error($rl)) {
                wp_send_json_error(['message' => $rl->get_error_message()]);
            }
        } elseif ($type === 'email') {
            if (!is_email($phone)) {
                wp_send_json_error(['message' => 'آدرس ایمیل نامعتبر است.']);
            }
        }

        $sent = Serene_Panel_OTP_Service::send_otp($phone, $type, $method);
        if (is_wp_error($sent)) {
            wp_send_json_error(['message' => $sent->get_error_message()]);
        }

        wp_send_json_success([
            'message' => 'کد تایید ارسال شد.',
            'phone'   => $phone,
        ]);
    }

    // 2. Verify OTP
    public function ajax_verify_otp() {
        Serene_Panel_Security::verify_nonce();

        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $code  = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';

        $phone = Serene_Panel_Auth::normalize_phone($phone);
        $verify = Serene_Panel_OTP_Service::verify_code($phone, $code);

        if (is_wp_error($verify)) {
            wp_send_json_error(['message' => $verify->get_error_message()]);
        }

        $user = Serene_Panel_Auth::create_or_get_user_by_phone($phone);
        if (is_wp_error($user)) {
            wp_send_json_error(['message' => $user->get_error_message()]);
        }

        // Check if user has 2FA enabled
        $two_fa_secret = get_user_meta($user->ID, '_serene_2fa_secret', true);
        if ($two_fa_secret) {
            set_transient('serene_2fa_pending_' . $user->ID, 1, 300);
            wp_send_json_success([
                'require_2fa' => true,
                'user_id'     => $user->ID,
                'message'     => 'لطفاً کد تایید دو مرحله‌ای (Google Authenticator) را وارد کنید.',
            ]);
        }

        Serene_Panel_Auth::login_user($user->ID);

        wp_send_json_success([
            'redirect' => home_url('/panel/'),
            'message'  => 'خوش آمدید! ورود موفقیت‌آمیز بود.',
        ]);
    }

    // 3. WebAuthn Registration Options
    public function ajax_webauthn_reg_options() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error(['message' => 'لطفاً ابتدا وارد شوید.']);

        $options = Serene_Panel_WebAuthn::get_register_options(get_current_user_id());
        wp_send_json_success($options);
    }

    public function ajax_webauthn_reg_save() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error(['message' => 'لطفاً ابتدا وارد شوید.']);

        $cred_id    = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';
        $public_key = isset($_POST['publicKey']) ? sanitize_text_field($_POST['publicKey']) : '';

        Serene_Panel_WebAuthn::save_registered_key(get_current_user_id(), $cred_id, $public_key);
        wp_send_json_success(['message' => 'اثر انگشت / کلید بیومتریک با موفقیت ثبت شد.']);
    }

    public function ajax_webauthn_login_options() {
        Serene_Panel_Security::verify_nonce();
        $options = Serene_Panel_WebAuthn::get_login_options();
        wp_send_json_success($options);
    }

    public function ajax_webauthn_login_verify() {
        Serene_Panel_Security::verify_nonce();
        $cred_id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';

        // Find user with this credential
        $users = get_users([
            'meta_key'   => '_serene_webauthn_keys',
            'number'     => 100,
        ]);

        $matched_user = null;
        foreach ($users as $u) {
            $keys = get_user_meta($u->ID, '_serene_webauthn_keys', true) ?: [];
            foreach ($keys as $k) {
                if (isset($k['id']) && $k['id'] === $cred_id) {
                    $matched_user = $u;
                    break 2;
                }
            }
        }

        if (!$matched_user) {
            wp_send_json_error(['message' => 'کلید بیومتریک در سیستم شناسایی نشد.']);
        }

        Serene_Panel_Auth::login_user($matched_user->ID);
        wp_send_json_success(['redirect' => home_url('/panel/'), 'message' => 'ورود با اثر انگشت موفقیت‌آمیز بود.']);
    }

    // 4. Two Factor TOTP
    public function ajax_2fa_setup() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error();

        $user_id = get_current_user_id();
        $secret  = Serene_Panel_Two_Factor::generate_secret();
        set_transient('serene_2fa_temp_' . $user_id, $secret, 600);

        $qr_url = Serene_Panel_Two_Factor::get_qr_code_url($user_id, $secret);
        wp_send_json_success(['secret' => $secret, 'qr_url' => $qr_url]);
    }

    public function ajax_2fa_verify() {
        Serene_Panel_Security::verify_nonce();
        $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';
        $user_id = is_user_logged_in() ? get_current_user_id() : intval($_POST['user_id']);

        $secret = get_user_meta($user_id, '_serene_2fa_secret', true);
        if (!$secret) {
            $secret = get_transient('serene_2fa_temp_' . $user_id);
            if ($secret && Serene_Panel_Two_Factor::verify_totp($secret, $code)) {
                update_user_meta($user_id, '_serene_2fa_secret', $secret);
                delete_transient('serene_2fa_temp_' . $user_id);
                wp_send_json_success(['message' => 'ورود دو مرحله‌ای با موفقیت فعال شد.']);
            }
        }

        if ($secret && Serene_Panel_Two_Factor::verify_totp($secret, $code)) {
            Serene_Panel_Auth::login_user($user_id);
            wp_send_json_success(['redirect' => home_url('/panel/'), 'message' => 'ورود تایید شد.']);
        }

        wp_send_json_error(['message' => 'کد ۶ رقمی Google Authenticator نامعتبر است.']);
    }

    public function ajax_2fa_disable() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error(['message' => 'لطفاً ابتدا وارد شوید.']);

        $user_id = get_current_user_id();
        delete_user_meta($user_id, '_serene_2fa_secret');
        wp_send_json_success(['message' => 'ورود دو مرحله‌ای با موفقیت غیرفعال شد.']);
    }

    public function ajax_webauthn_delete_key() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error();

        $user_id = get_current_user_id();
        $cred_id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';

        $keys = get_user_meta($user_id, '_serene_webauthn_keys', true) ?: [];
        $new_keys = [];
        foreach ($keys as $k) {
            if (isset($k['id']) && $k['id'] === $cred_id) continue;
            $new_keys[] = $k;
        }

        update_user_meta($user_id, '_serene_webauthn_keys', $new_keys);
        wp_send_json_success(['message' => 'کلید بیومتریک با موفقیت حذف شد.']);
    }

    // 5. Google Login
    public function ajax_google_login() {
        Serene_Panel_Security::verify_nonce();
        $id_token = isset($_POST['id_token']) ? sanitize_text_field($_POST['id_token']) : '';

        $data = Serene_Panel_Google_OAuth::verify_id_token($id_token);
        if (is_wp_error($data)) {
            wp_send_json_error(['message' => $data->get_error_message()]);
        }

        $email = sanitize_email($data['email']);
        $user  = get_user_by('email', $email);

        if (!$user) {
            $username = sanitize_user(explode('@', $email)[0]) . '_' . wp_rand(100, 999);
            $password = wp_generate_password(18);
            $user_id  = wp_create_user($username, $password, $email);

            if (is_wp_error($user_id)) {
                wp_send_json_error(['message' => $user_id->get_error_message()]);
            }

            $user = get_user_by('ID', $user_id);
            $user->set_role('customer');
            if (!empty($data['name'])) update_user_meta($user_id, 'first_name', $data['name']);
            if (!empty($data['picture'])) update_user_meta($user_id, '_serene_avatar_url', $data['picture']);
        }

        Serene_Panel_Auth::login_user($user->ID);
        wp_send_json_success(['redirect' => home_url('/panel/'), 'message' => 'ورود با گوگل با موفقیت انجام شد.']);
    }

    // 6. Profile Update
    public function ajax_update_profile() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error();

        $user_id = get_current_user_id();
        $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name  = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $email      = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

        wp_update_user([
            'ID'         => $user_id,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'user_email' => $email,
        ]);

        // Dynamic Custom fields
        $custom_fields = Serene_Panel_Form_Builder::get_custom_fields();
        foreach ($custom_fields as $f_key => $f_def) {
            if (isset($_POST[$f_key])) {
                $meta_key = $f_def['meta_key'] ?? ('_serene_' . $f_key);
                update_user_meta($user_id, $meta_key, sanitize_text_field($_POST[$f_key]));
            }
        }

        wp_send_json_success(['message' => 'اطلاعات پروفایل با موفقیت ذخیره شد.']);
    }

    // 7. Avatar Upload
    public function ajax_upload_avatar() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error();

        if (empty($_FILES['avatar'])) {
            wp_send_json_error(['message' => 'فایلی انتخاب نشده است.']);
        }

        $url = Serene_Panel_Profile::save_avatar(get_current_user_id(), $_FILES['avatar']);
        if (is_wp_error($url)) {
            wp_send_json_error(['message' => $url->get_error_message()]);
        }

        wp_send_json_success(['url' => $url, 'message' => 'تصویر پروفایل به‌روزرسانی شد.']);
    }

    // 8. Card to Card Submit
    public function ajax_c2c_submit() {
        Serene_Panel_Security::verify_nonce();

        $order_id = intval($_POST['order_id']);
        $tracking = sanitize_text_field($_POST['tracking_code'] ?? '');
        $card     = sanitize_text_field($_POST['card_number'] ?? '');
        
        $order = wc_get_order($order_id);
        if (!$order) {
            wp_send_json_error(['message' => 'سفارش معتبر یافت نشد.']);
        }

        $amount = floatval($_POST['amount'] ?? $order->get_total());

        // Handle file upload
        $receipt_url = '';
        $receipt_file = !empty($_FILES['receipt_file']) ? $_FILES['receipt_file'] : (!empty($_FILES['receipt_image']) ? $_FILES['receipt_image'] : null);
        if ($receipt_file && !empty($receipt_file['tmp_name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $upload = wp_handle_upload($receipt_file, ['test_form' => false]);
            if (!isset($upload['error']) && isset($upload['url'])) {
                $receipt_url = $upload['url'];
            }
        }

        update_post_meta($order_id, '_c2c_payer_card', $card);
        update_post_meta($order_id, '_c2c_tracking_code', $tracking);
        if ($receipt_url) {
            update_post_meta($order_id, '_c2c_receipt_url', $receipt_url);
        }
        update_post_meta($order_id, '_c2c_status', 'pending');

        if ($order) {
            $order->update_meta_data('_c2c_payer_card', $card);
            $order->update_meta_data('_c2c_tracking_code', $tracking);
            if ($receipt_url) {
                $order->update_meta_data('_c2c_receipt_url', $receipt_url);
            }
            $order->update_meta_data('_c2c_status', 'pending');
            $order->save();
        }

        global $wpdb;
        $table = $wpdb->prefix . 'serene_card_transfers';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") === $table) {
            $wpdb->insert($table, [
                'order_id'      => $order_id,
                'user_id'       => get_current_user_id() ?: ($order ? $order->get_customer_id() : 0),
                'card_number'   => $card,
                'tracking_code' => $tracking,
                'amount'        => $amount,
                'status'        => 'pending',
                'receipt_image' => $receipt_url ?: '',
                'created_at'    => current_time('mysql'),
            ]);
        }

        if ($order) {
            $order->update_status('on-hold', sprintf('رسید کارت‌به‌کارت با کد پیگیری %s ثبت شد.', $tracking ?: 'تصویر فیش'));
        }

        if (class_exists('Serene_Panel_System_Logger')) {
            Serene_Panel_System_Logger::info('C2C', sprintf('فیش کارت‌به‌کارت برای سفارش #%d ثبت شد.', $order_id));
        }

        wp_send_json_success(['message' => 'فیش واریزی و کد پیگیری شما با موفقیت ثبت گردید و پس از بررسی تایید خواهد شد.']);
    }

    // 9. Wallet Transfer
    public function ajax_wallet_transfer() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error(['message' => 'لطفاً ابتدا وارد حساب کاربری خود شوید.']);

        $user_id = get_current_user_id();
        $target_phone = sanitize_text_field($_POST['target_phone'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);

        // 1. Rate Limiting: Max 5 transfers per 60 seconds
        $rate_key = 'serene_rate_transfer_' . $user_id;
        $attempts = (int) get_transient($rate_key);
        if ($attempts >= 5) {
            wp_send_json_error(['message' => 'تعداد درخواست‌های انتقال بیش از حد مجاز است. لطفاً ۱ دقیقه دیگر مجدداً تلاش فرمایید.']);
        }
        set_transient($rate_key, $attempts + 1, 60);

        // 2. Amount Validation
        $options = get_option('serene_panel_options', []);
        $min_transfer = floatval($options['wallet_min_transfer'] ?? 10000);

        if ($amount <= 0) {
            wp_send_json_error(['message' => 'مبلغ انتقال نامعتبر است.']);
        }

        if ($amount < $min_transfer) {
            wp_send_json_error(['message' => sprintf('حداقل مبلغ مجاز جهت انتقال %s تومان است.', number_format($min_transfer))]);
        }

        // 3. Balance Check
        $current_balance = Serene_Panel_Wallet::get_balance($user_id);
        if ($current_balance < $amount) {
            wp_send_json_error(['message' => 'موجودی کیف پول شما برای این انتقال کافی نمی‌باشد.']);
        }

        // 4. Target User Resolution
        $clean_phone = Serene_Panel_Auth::normalize_phone($target_phone);
        $target_user = Serene_Panel_Auth::get_user_by_phone($clean_phone);
        if (!$target_user || (int) $target_user->ID === (int) $user_id) {
            wp_send_json_error(['message' => 'کاربر مقصد با این شماره همراه یافت نشد یا نمی‌توانید به حساب خودتان انتقال دهید.']);
        }

        // 5. Execute Debit & Credit
        $sender_user = wp_get_current_user();
        $sender_name = $sender_user->display_name ?: $sender_user->user_login;
        $target_name = $target_user->display_name ?: $target_user->user_login;

        $debit = Serene_Panel_Wallet::update_balance($user_id, -$amount, 'transfer_out', sprintf('انتقال وجه به %s (%s)', $target_name, $clean_phone));
        if (is_wp_error($debit)) {
            wp_send_json_error(['message' => $debit->get_error_message()]);
        }

        Serene_Panel_Wallet::update_balance($target_user->ID, $amount, 'transfer_in', sprintf('دریافت وجه از %s', $sender_name));

        if (class_exists('Serene_Panel_System_Logger')) {
            Serene_Panel_System_Logger::success('WALLET_TRANSFER', sprintf('انتقال %s تومان از کاربر #%d به کاربر #%d با موفقیت انجام شد.', number_format($amount), $user_id, $target_user->ID));
        }

        wp_send_json_success([
            'message'     => sprintf('مبلغ %s تومان با موفقیت به کیف پول %s انتقال یافت.', number_format($amount), $target_name),
            'new_balance' => Serene_Panel_Wallet::get_balance($user_id),
        ]);
    }

    // 10. Tickets
    public function ajax_create_ticket() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error();

        $subject    = sanitize_text_field($_POST['subject']);
        $department = sanitize_text_field($_POST['department']);
        $priority   = sanitize_text_field($_POST['priority']);
        $message    = sanitize_textarea_field($_POST['message']);

        $ticket_id = Serene_Panel_Tickets::create_ticket(get_current_user_id(), $subject, $department, $priority, $message);
        wp_send_json_success(['ticket_id' => $ticket_id, 'message' => 'تیکت شما با موفقیت ثبت شد.']);
    }

    public function ajax_reply_ticket() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error();

        $ticket_id = intval($_POST['ticket_id']);
        $message   = sanitize_textarea_field($_POST['message']);

        Serene_Panel_Tickets::reply_ticket($ticket_id, get_current_user_id(), $message, 0);
        wp_send_json_success(['message' => 'پاسخ شما ارسال شد.']);
    }

    // 11. Lucky Wheel Spin
    public function ajax_spin_wheel() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error(['message' => 'برای چرخش گردونه ابتدا باید وارد شوید.']);

        $result = Serene_Panel_Lucky_Wheel::spin(get_current_user_id());
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success($result);
    }

    // 12. Smart Review
    public function ajax_submit_review() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error();

        $product_id = intval($_POST['product_id']);
        $rating     = intval($_POST['rating']);
        $comment    = sanitize_textarea_field($_POST['comment']);

        $res = Serene_Panel_Smart_Reviews::submit_review(get_current_user_id(), $product_id, $rating, $comment);
        if (is_wp_error($res)) {
            wp_send_json_error(['message' => $res->get_error_message()]);
        }

        wp_send_json_success([
            'message' => sprintf('دیدگاه شما با موفقیت ثبت شد و مبلغ %s تومان پاداش به کیف پول شما واریز گردید!', number_format($res['reward'])),
        ]);
    }

    public function ajax_revoke_session() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error();

        global $wpdb;
        $session_id = intval($_POST['session_id']);
        $table = $wpdb->prefix . 'serene_active_sessions';
        $wpdb->update($table, ['is_revoked' => 1], ['id' => $session_id, 'user_id' => get_current_user_id()]);

        wp_send_json_success(['message' => 'دسترسی نشست با موفقیت قطع شد.']);
    }

    public function ajax_revoke_all_sessions() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error();

        global $wpdb;
        $current_token = isset($_COOKIE['serene_session_token']) ? sanitize_text_field($_COOKIE['serene_session_token']) : '';
        $table = $wpdb->prefix . 'serene_active_sessions';
        
        $wpdb->query($wpdb->prepare(
            "UPDATE $table SET is_revoked = 1 WHERE user_id = %d AND session_token != %s",
            get_current_user_id(),
            $current_token
        ));

        wp_send_json_success(['message' => 'کلیه نشست‌های دیگر با موفقیت بسته شدند.']);
    }

    public function ajax_create_rma() {
        Serene_Panel_Security::verify_nonce();
        if (!is_user_logged_in()) wp_send_json_error();

        $order_id = intval($_POST['order_id']);
        $reason   = sanitize_textarea_field($_POST['reason']);
        $res = Serene_Panel_RMA::create_request(get_current_user_id(), $order_id, $reason);

        if (is_wp_error($res)) {
            wp_send_json_error(['message' => $res->get_error_message()]);
        }

        wp_send_json_success(['message' => 'درخواست مرجوعی کالا با موفقیت ثبت شد و در دست بررسی است.']);
    }

    // 13. Admin Settings Save
    public function ajax_admin_save_settings() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $options = isset($_POST['settings']) ? (array) $_POST['settings'] : [];
        $sanitized = [];

        foreach ($options as $k => $v) {
            $sanitized[sanitize_key($k)] = is_array($v) ? array_map('sanitize_text_field', $v) : sanitize_text_field($v);
        }

        update_option('serene_panel_options', $sanitized);

        // Sync Card-to-Card options directly to WooCommerce gateway settings
        $wc_c2c_settings = get_option('woocommerce_palette_c2c_settings', []);
        $wc_c2c_settings['enabled']      = !empty($sanitized['enable_c2c']) ? 'yes' : 'no';
        $wc_c2c_settings['title']        = !empty($sanitized['c2c_title']) ? $sanitized['c2c_title'] : 'کارت به کارت خودکار و هوشمند';
        $wc_c2c_settings['description']  = !empty($sanitized['c2c_desc']) ? $sanitized['c2c_desc'] : 'مبلغ سفارش را به شماره کارت فروشگاه واریز فرموده و رسید یا کد رهگیری را ثبت نمایید.';
        $wc_c2c_settings['card_number']  = $sanitized['c2c_card_number'] ?? '';
        $wc_c2c_settings['card_holder']  = $sanitized['c2c_card_holder'] ?? '';
        $wc_c2c_settings['bank_name']    = $sanitized['c2c_bank_name'] ?? '';
        $wc_c2c_settings['sheba_number'] = $sanitized['c2c_sheba_number'] ?? '';
        update_option('woocommerce_palette_c2c_settings', $wc_c2c_settings);

        // Wheel Slices
        if (isset($_POST['wheel_slices']) && is_array($_POST['wheel_slices'])) {
            $slices = [];
            foreach ($_POST['wheel_slices'] as $index => $sl) {
                $slices[] = [
                    'id'     => intval($index),
                    'label'  => sanitize_text_field($sl['label'] ?? ''),
                    'type'   => sanitize_key($sl['type'] ?? 'empty'),
                    'value'  => sanitize_text_field($sl['value'] ?? ''),
                    'weight' => intval($sl['weight'] ?? 10),
                    'color'  => sanitize_hex_color($sl['color'] ?? '#4c5e8b') ?: '#4c5e8b',
                    'text'   => sanitize_hex_color($sl['text'] ?? '#ffffff') ?: '#ffffff',
                ];
            }
            update_option('serene_lucky_wheel_slices', $slices);
        }

        // Live Chat Channels Builder
        if (isset($_POST['chat_channels']) && is_array($_POST['chat_channels'])) {
            $channels = [];
            foreach ($_POST['chat_channels'] as $cid => $ch) {
                $clean_cid = sanitize_key($cid);
                $channels[$clean_cid] = [
                    'id'       => $clean_cid,
                    'title'    => sanitize_text_field($ch['title'] ?? ''),
                    'subtitle' => sanitize_text_field($ch['subtitle'] ?? ''),
                    'icon'     => sanitize_text_field($ch['icon'] ?? 'chat'),
                    'type'     => sanitize_key($ch['type'] ?? 'custom'),
                    'url'      => sanitize_text_field($ch['url'] ?? ''),
                    'color'    => sanitize_hex_color($ch['color'] ?? '#4c5e8b') ?: '#4c5e8b',
                    'enabled'  => !empty($ch['enabled']) ? 1 : 0,
                    'order'    => intval($ch['order'] ?? 0),
                ];
            }
            update_option('serene_panel_chat_channels', $channels);
        }

        // Menu Builder
        $submitted_menu = isset($_POST['menu_items']) ? $_POST['menu_items'] : (isset($_POST['custom_menu']) ? $_POST['custom_menu'] : null);
        if ($submitted_menu && is_array($submitted_menu)) {
            $menu = [];
            foreach ($submitted_menu as $k => $m) {
                $menu[sanitize_key($k)] = [
                    'id'      => sanitize_key($m['id'] ?? $k),
                    'title'   => sanitize_text_field($m['title'] ?? ''),
                    'icon'    => sanitize_text_field($m['icon'] ?? 'star'),
                    'type'    => sanitize_key($m['type'] ?? 'endpoint'),
                    'target'  => sanitize_text_field($m['target'] ?? ''),
                    'badge'   => sanitize_text_field($m['badge'] ?? ''),
                    'roles'   => sanitize_text_field($m['roles'] ?? 'all'),
                    'enabled' => !empty($m['enabled']) ? 1 : 0,
                    'order'   => intval($m['order'] ?? 0),
                ];
            }
            update_option('serene_panel_custom_menu', $menu);
        }

        // Custom Fields Builder
        if (isset($_POST['custom_fields']) && is_array($_POST['custom_fields'])) {
            $fields = [];
            foreach ($_POST['custom_fields'] as $k => $f) {
                $raw_key = !empty($f['key']) ? $f['key'] : $k;
                $clean_key = sanitize_key($raw_key);
                if (empty($clean_key)) continue;

                $fields[$clean_key] = [
                    'id'               => $clean_key,
                    'key'              => $clean_key,
                    'label'            => sanitize_text_field($f['label'] ?? ''),
                    'type'             => sanitize_key($f['type'] ?? 'text'),
                    'enabled'          => 1,
                    'required'         => !empty($f['required']) ? 1 : 0,
                    'show_in_register' => !empty($f['show_in_register']) ? 1 : 0,
                    'show_in_checkout' => !empty($f['show_in_checkout']) ? 1 : 0,
                    'placeholder'      => sanitize_text_field($f['placeholder'] ?? ''),
                    'meta_key'         => sanitize_text_field($f['meta_key'] ?? ('_serene_' . $clean_key)),
                ];
            }
            update_option('serene_panel_custom_fields', $fields);
        }

        // Global Theme Colors Sync
        $theme_colors = get_option('serene_panel_theme_colors', []);
        if (!is_array($theme_colors)) $theme_colors = [];
        if (!empty($sanitized['color_primary'])) {
            $theme_colors['primary'] = $sanitized['color_primary'];
            $theme_colors['primary_dim'] = $sanitized['color_primary'];
        }
        if (!empty($sanitized['color_secondary'])) {
            $theme_colors['secondary'] = $sanitized['color_secondary'];
        }
        if (!empty($sanitized['color_tertiary'])) {
            $theme_colors['tertiary'] = $sanitized['color_tertiary'];
        }
        update_option('serene_panel_theme_colors', $theme_colors);

        if (class_exists('Serene_Panel_System_Logger')) {
            Serene_Panel_System_Logger::success('SYSTEM', 'تنظیمات کلی پنل کاربری ذخیره و به‌روزرسانی شد.');
        }

        wp_send_json_success(['message' => 'کلیه تنظیمات با موفقیت ذخیره شد.']);
    }

    // 15. Admin Clear Logs
    public function ajax_admin_clear_logs() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        Serene_Panel_System_Logger::clear_logs();
        wp_send_json_success(['message' => 'کلیه لاگ‌های سیستم با موفقیت پاکسازی شدند.']);
    }

    // 16. Admin Get Logs
    public function ajax_admin_get_logs() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $channel = isset($_POST['channel']) ? sanitize_text_field($_POST['channel']) : '';
        $level   = isset($_POST['level']) ? sanitize_text_field($_POST['level']) : '';
        $search  = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

        $logs = Serene_Panel_System_Logger::get_logs(100, $channel, $level, $search);
        wp_send_json_success(['logs' => $logs]);
    }

    // 14. Admin Test SMS
    public function ajax_admin_test_sms() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $phone = sanitize_text_field($_POST['phone']);
        $res = Serene_Panel_OTP_Service::send_otp($phone, 'sms', 'text');

        if (is_wp_error($res)) {
            wp_send_json_error(['message' => $res->get_error_message()]);
        }

        wp_send_json_success(['message' => 'پیامک تستی با موفقیت ارسال شد.']);
    }

    // 17. Admin Search Users
    public function ajax_admin_search_users() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $term = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';
        if (strlen($term) < 2) {
            wp_send_json_success(['users' => []]);
        }

        $query = new WP_User_Query([
            'search'         => '*' . esc_attr($term) . '*',
            'search_columns' => ['user_login', 'user_nicename', 'user_email', 'display_name'],
            'number'         => 15,
        ]);

        $users = $query->get_results();
        $results = [];

        foreach ($users as $u) {
            $phone = get_user_meta($u->ID, 'billing_phone', true);
            $balance = Serene_Panel_Wallet::get_balance($u->ID);

            $results[] = [
                'id'                => $u->ID,
                'name'              => $u->display_name ?: $u->user_login,
                'login'             => $u->user_login,
                'email'             => $u->user_email,
                'phone'             => $phone ?: 'بدون شماره',
                'balance'           => $balance,
                'balance_formatted' => number_format($balance),
                'avatar'            => get_avatar_url($u->ID, ['size' => 64]),
            ];
        }

        wp_send_json_success(['users' => $results]);
    }

    // 18. Admin Adjust User Wallet
    public function ajax_admin_adjust_wallet() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $user_id     = intval($_POST['user_id']);
        $amount      = floatval($_POST['amount']);
        $action_type = sanitize_key($_POST['action_type']); // 'add' or 'deduct'
        $reason      = sanitize_text_field($_POST['reason']);

        if (!$user_id || $amount <= 0) {
            wp_send_json_error(['message' => 'لطفاً کاربر و مبلغ معتبر وارد نمایید.']);
        }

        if (empty($reason)) {
            $reason = ($action_type === 'add') ? 'افزایش اعتبار دستی توسط مدیر سایت' : 'کاهش اعتبار دستی توسط مدیر سایت';
        }

        $type = ($action_type === 'add') ? 'credit' : 'debit';
        $new_balance = Serene_Panel_Wallet::update_balance($user_id, $amount, $type, $reason);

        if (class_exists('Serene_Panel_System_Logger')) {
            Serene_Panel_System_Logger::success('WALLET_ADMIN', sprintf('تغییر موجودی کاربر #%d به مبلغ %s تومان (%s)', $user_id, number_format($amount), $action_type));
        }

        wp_send_json_success([
            'message'           => sprintf('موجودی کیف پول کاربر با موفقیت به‌روزرسانی شد. موجودی جدید: %s تومان', number_format($new_balance)),
            'new_balance'       => $new_balance,
            'balance_formatted' => number_format($new_balance),
        ]);
    }

    // 19. Admin Get Ticket Details & Messages
    public function ajax_admin_get_ticket_details() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $ticket_id = intval($_POST['ticket_id']);
        $ticket = Serene_Panel_Tickets::get_ticket($ticket_id);
        if (!$ticket) {
            wp_send_json_error(['message' => 'تیکت مورد نظر یافت نشد.']);
        }

        $user = get_user_by('ID', $ticket->user_id);
        $departments = Serene_Panel_Tickets::get_departments();

        $ticket->user_name = $user ? ($user->display_name ?: $user->user_login) : 'کاربر مهمان #' . $ticket->user_id;
        $ticket->department_name = $departments[$ticket->department] ?? $ticket->department;

        $messages = Serene_Panel_Tickets::get_ticket_messages($ticket_id);

        wp_send_json_success([
            'ticket'   => $ticket,
            'messages' => $messages,
        ]);
    }

    // 20. Admin Reply Ticket
    public function ajax_admin_reply_ticket() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $ticket_id    = intval($_POST['ticket_id']);
        $message      = sanitize_textarea_field($_POST['message']);
        $close_ticket = !empty($_POST['close_ticket']);

        if (!$ticket_id || empty($message)) {
            wp_send_json_error(['message' => 'لطفاً متن پاسخ را وارد نمایید.']);
        }

        $admin_id = get_current_user_id();
        Serene_Panel_Tickets::reply_ticket($ticket_id, $admin_id, $message, 1);

        if ($close_ticket) {
            Serene_Panel_Tickets::update_ticket_status($ticket_id, 'closed');
        }

        if (class_exists('Serene_Panel_System_Logger')) {
            Serene_Panel_System_Logger::info('TICKETS', sprintf('پاسخ مدیر به تیکت #%d ثبت شد.', $ticket_id));
        }

        wp_send_json_success(['message' => 'پاسخ شما با موفقیت برای کاربر ارسال گردید.']);
    }

    // 21. Admin Update Ticket Status
    public function ajax_admin_update_ticket_status() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $ticket_id = intval($_POST['ticket_id']);
        $status    = sanitize_key($_POST['status']);

        if (!$ticket_id || empty($status)) {
            wp_send_json_error(['message' => 'اطلاعات ناقص است.']);
        }

        Serene_Panel_Tickets::update_ticket_status($ticket_id, $status);
        wp_send_json_success(['message' => 'وضعیت تیکت با موفقیت تغییر یافت.']);
    }

    // 22. Admin Update Ticket Priority
    public function ajax_admin_update_ticket_priority() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $ticket_id = intval($_POST['ticket_id']);
        $priority  = sanitize_key($_POST['priority']);

        if (!$ticket_id || empty($priority)) {
            wp_send_json_error(['message' => 'اطلاعات ناقص است.']);
        }

        Serene_Panel_Tickets::update_ticket_priority($ticket_id, $priority);
        wp_send_json_success(['message' => 'اولویت تیکت با موفقیت به‌روزرسانی شد.']);
    }

    // 23. Admin Delete Ticket
    public function ajax_admin_delete_ticket() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $ticket_id = intval($_POST['ticket_id']);
        if (!$ticket_id) {
            wp_send_json_error(['message' => 'شناسه تیکت نامعتبر است.']);
        }

        Serene_Panel_Tickets::delete_ticket($ticket_id);
        wp_send_json_success(['message' => 'تیکت و کلیه پیام‌های آن با موفقیت حذف شدند.']);
    }

    // 24. Admin Save Departments
    public function ajax_admin_save_departments() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $raw = isset($_POST['departments']) ? stripslashes($_POST['departments']) : '';
        $depts = json_decode($raw, true);

        if (!is_array($depts) || empty($depts)) {
            wp_send_json_error(['message' => 'حداقل یک دپارتمان باید وجود داشته باشد.']);
        }

        Serene_Panel_Tickets::save_departments($depts);
        wp_send_json_success(['message' => 'دپارتمان‌ها با موفقیت ذخیره شدند.']);
    }

    // 25. Admin Verify C2C Receipt
    public function ajax_admin_verify_c2c_receipt() {
        if (!current_user_can('manage_options') && !current_user_can('edit_shop_orders')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $order_id = intval($_POST['order_id'] ?? 0);
        $record_id = intval($_POST['record_id'] ?? 0);
        $verify_action = sanitize_key($_POST['verify_action'] ?? ($_POST['review_status'] ?? 'approve'));
        if ($verify_action === 'approved') $verify_action = 'approve';
        if ($verify_action === 'rejected') $verify_action = 'reject';

        global $wpdb;
        $table = $wpdb->prefix . 'serene_card_transfers';

        if (!$order_id && $record_id) {
            $rec = $wpdb->get_row($wpdb->prepare("SELECT order_id FROM $table WHERE id = %d", $record_id));
            if ($rec) {
                $order_id = intval($rec->order_id);
            }
        }

        $order = wc_get_order($order_id);
        if (!$order) wp_send_json_error(['message' => 'سفارش یافت نشد.']);

        if ($verify_action === 'approve') {
            update_post_meta($order_id, '_c2c_status', 'approved');
            $wpdb->update($table, ['status' => 'approved', 'matched_at' => current_time('mysql')], ['order_id' => $order_id]);
            $order->payment_complete();
            $order->add_order_note('فیش واریز کارت‌به‌کارت توسط مدیر سایت تایید گردید.');
            
            if (class_exists('Serene_Panel_System_Logger')) {
                Serene_Panel_System_Logger::success('C2C', sprintf('فیش سفارش #%d توسط مدیر تایید شد.', $order_id));
            }
            
            wp_send_json_success(['message' => 'فیش با موفقیت تایید و وضعیت سفارش به تکمیل شده/در حال انجام تغییر یافت.']);
        } else {
            update_post_meta($order_id, '_c2c_status', 'rejected');
            $wpdb->update($table, ['status' => 'rejected'], ['order_id' => $order_id]);
            $order->add_order_note('فیش واریز کارت‌به‌کارت توسط مدیر سایت رد شد.');
            
            if (class_exists('Serene_Panel_System_Logger')) {
                Serene_Panel_System_Logger::warning('C2C', sprintf('فیش سفارش #%d توسط مدیر رد شد.', $order_id));
            }
            
            wp_send_json_success(['message' => 'فیش با موفقیت رد شد.']);
        }
    }

    // 25.1. Admin Learn & Save Custom Bank SMS Pattern
    public function ajax_admin_learn_bank_pattern() {
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'دسترسی غیرمجاز.']);
        Serene_Panel_Security::verify_nonce();

        $bank_name = sanitize_text_field($_POST['bank_name'] ?? '');
        $sample_sms = sanitize_textarea_field($_POST['sample_sms'] ?? '');
        $unit = sanitize_key($_POST['currency_unit'] ?? 'rial'); // 'rial' or 'toman'
        $custom_regex = isset($_POST['custom_regex']) ? trim(wp_unslash($_POST['custom_regex'])) : '';

        if (empty($bank_name)) {
            wp_send_json_error(['message' => 'لطفاً نام بانک یا مؤسسه را وارد فرمایید.']);
        }
        if (empty($sample_sms)) {
            wp_send_json_error(['message' => 'لطفاً نمونه پیامک دریافتی از بانک را وارد نمایید.']);
        }

        // Clean SMS
        $clean = str_replace([',', '،', 'ي', 'ك'], ['', '', 'ی', 'ک'], $sample_sms);
        $farsi = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $latin = ['0','1','2','3','4','5','6','7','8','9'];
        $clean = str_replace($farsi, $latin, $clean);

        $regex = $custom_regex;
        if (empty($regex)) {
            if (preg_match('/(\d{4,14})\s*(ریال|تومان)?\s*(?:به حساب|نشست)/us', $clean)) {
                $regex = '/(\d+)\s*(ریال|تومان)?\s*(?:به حساب|نشست)/us';
            } elseif (preg_match('/مبلغ\s*[:=]?\s*(\d{4,14})/us', $clean)) {
                $regex = '/مبلغ\s*[:=]?\s*(\d{4,14})\s*(ریال|تومان)?/us';
            } elseif (preg_match('/(?:واریز|انتقال|واریز پول|واریز به|شارژ)\s*[:=]?\s*(\d{4,14})/us', $clean)) {
                $regex = '/(?:واریز|انتقال|واریز پول|واریز به|شارژ)\s*[:=]?\s*(\d{4,14})\s*(ریال|تومان)?/us';
            } else {
                $regex = '/(\d{4,14})\s*(ریال|تومان)?/us';
            }
        }

        // Test regex on sample
        $matched = preg_match($regex, $clean, $m);
        if (!$matched) {
            wp_send_json_error(['message' => 'الگوی تولیدشده نتوانست مبلغ را از نمونه پیامک استخراج کند. لطفاً نمونه متن را بررسی نمایید.']);
        }

        $raw_amount = floatval($m[1] ?? 0);
        $amount_toman = ($unit === 'toman') ? $raw_amount : ($raw_amount / 10);

        $patterns = get_option('palette_custom_bank_patterns', []);
        if (!is_array($patterns)) $patterns = [];

        $pattern_id = 'pattern_' . time() . '_' . wp_rand(100, 999);
        $new_entry = [
            'id'          => $pattern_id,
            'bank_name'   => $bank_name,
            'unit'        => $unit,
            'regex'       => $regex,
            'sample_sms'  => $sample_sms,
            'created_at'  => current_time('mysql')
        ];

        $patterns[] = $new_entry;
        update_option('palette_custom_bank_patterns', $patterns);

        if (class_exists('Serene_Panel_System_Logger')) {
            Serene_Panel_System_Logger::success('C2C', sprintf('الگوی جدید برای %s با موفقیت یاد گرفته و ذخیره شد.', $bank_name));
        }

        wp_send_json_success([
            'message'      => sprintf('الگوی پیامک %s با موفقیت یاد گرفته و ذخیره شد! (مبلغ استخراج‌شده: %s تومان)', $bank_name, number_format($amount_toman)),
            'pattern'      => $new_entry,
            'parsed_amount'=> $amount_toman,
            'all_patterns' => $patterns
        ]);
    }

    // 25.2. Admin Delete Bank Pattern
    public function ajax_admin_delete_bank_pattern() {
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'دسترسی غیرمجاز.']);
        Serene_Panel_Security::verify_nonce();

        $pattern_id = sanitize_text_field($_POST['pattern_id'] ?? '');
        $patterns = get_option('palette_custom_bank_patterns', []);
        if (!is_array($patterns)) $patterns = [];

        $filtered = array_values(array_filter($patterns, function($p) use ($pattern_id) {
            return ($p['id'] ?? '') !== $pattern_id;
        }));

        update_option('palette_custom_bank_patterns', $filtered);

        wp_send_json_success([
            'message'      => 'الگوی پیامک با موفقیت حذف شد.',
            'all_patterns' => $filtered
        ]);
    }

    // 26. Admin Save Shipping Carriers
    public function ajax_admin_save_carriers() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $raw = isset($_POST['carriers']) ? stripslashes($_POST['carriers']) : '';
        $carriers = json_decode($raw, true);

        if (!is_array($carriers) || empty($carriers)) {
            wp_send_json_error(['message' => 'حداقل یک شرکت پستی باید ثبت شده باشد.']);
        }

        Serene_Panel_Post_Tracker::save_carriers($carriers);
        wp_send_json_success(['message' => 'لیست شرکت‌های پستی با موفقیت ذخیره شد.']);
    }

    // 27. Admin Bulk Adjust Wallet Balance
    public function ajax_admin_bulk_adjust_wallet() {
        if (!current_user_can('manage_options')) wp_send_json_error();
        Serene_Panel_Security::verify_nonce();

        $target_role = sanitize_text_field($_POST['target_role'] ?? 'all');
        $action_type = sanitize_key($_POST['action_type'] ?? 'add'); // 'add' or 'deduct'
        $amount      = floatval($_POST['amount'] ?? 0);
        $reason      = sanitize_text_field($_POST['reason'] ?? '');
        $send_sms    = !empty($_POST['send_sms']);
        $sms_pattern = sanitize_text_field($_POST['sms_pattern'] ?? '');

        if ($amount <= 0) {
            wp_send_json_error(['message' => 'لطفاً مبلغ معتبر وارد نمایید.']);
        }

        if (empty($reason)) {
            $reason = ($action_type === 'add') ? 'شارژ گروهی توسط مدیریت سایت' : 'کسر اعتبار گروهی توسط مدیریت سایت';
        }

        $args = ['fields' => ['ID', 'display_name', 'user_email', 'user_login'], 'number' => 1000];
        if ($target_role !== 'all') {
            $args['role'] = $target_role;
        }

        $users = get_users($args);
        $count = 0;
        $type = ($action_type === 'add') ? 'credit' : 'debit';

        foreach ($users as $u) {
            $new_bal = Serene_Panel_Wallet::update_balance($u->ID, $amount, $type, $reason);
            $count++;

            // Optional Pattern-based SMS Notification
            if ($send_sms) {
                $phone = get_user_meta($u->ID, 'billing_phone', true);
                if (!empty($phone)) {
                    $name = $u->display_name ?: $u->user_login;
                    $msg = sprintf(
                        "کاربر گرامی %s، مبلغ %s تومان به کیف پول شما %s شد.\nعلت: %s\nموجودی جدید: %s تومان",
                        $name,
                        number_format($amount),
                        ($action_type === 'add' ? 'واریز' : 'کسر'),
                        $reason,
                        number_format(is_numeric($new_bal) ? $new_bal : 0)
                    );
                    Serene_Panel_OTP_Service::send_sms($phone, $msg, $sms_pattern);
                }
            }
        }

        if (class_exists('Serene_Panel_System_Logger')) {
            Serene_Panel_System_Logger::success('WALLET_BULK', sprintf('تغییر اعتبار گروهی برای %d کاربر با موفقیت اعمال شد. (مبلغ: %s تومان)', $count, number_format($amount)));
        }

        wp_send_json_success([
            'message' => sprintf('اعتبار کیف پول %d کاربر با موفقیت به‌روزرسانی شد.', $count),
            'count'   => $count,
        ]);
    }

    // 16. Username & Password Login
    public function ajax_login_with_password() {
        Serene_Panel_Security::verify_nonce();

        $log = isset($_POST['log']) ? sanitize_text_field($_POST['log']) : '';
        $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';
        $remember = !empty($_POST['rememberme']);

        if (empty($log) || empty($pwd)) {
            wp_send_json_error(['message' => 'لطفاً نام کاربری و کلمه عبور را وارد نمایید.']);
        }

        // If phone number entered as username, look up matching username
        if (preg_match('/^09[0-9]{9}$/', $log) || preg_match('/^9[0-9]{9}$/', $log)) {
            $user_by_phone = Serene_Panel_Auth::get_user_by_phone($log);
            if ($user_by_phone) {
                $log = $user_by_phone->user_login;
            }
        }

        $creds = [
            'user_login'    => $log,
            'user_password' => $pwd,
            'remember'      => $remember,
        ];

        $user = wp_signon($creds, is_ssl());

        if (is_wp_error($user)) {
            wp_send_json_error(['message' => 'نام کاربری، ایمیل یا رمز عبور وارد شده نادرست است.']);
        }

        // Record session
        if (class_exists('Serene_Panel_Session_Manager')) {
            Serene_Panel_Session_Manager::record_session($user->ID);
        }

        $redirect_url = wc_get_page_permalink('myaccount');
        if (user_can($user, 'manage_options')) {
            $opt = get_option('serene_panel_options', []);
            $redirect_url = (!empty($opt['login_role_target']) && $opt['login_role_target'] === 'customers_only') ? admin_url() : wc_get_page_permalink('myaccount');
        }

        wp_send_json_success([
            'message'      => 'ورود با موفقیت انجام شد.',
            'redirect_url' => $redirect_url,
        ]);
    }

    // 17. Subscribe Price & Stock Alert
    public function ajax_subscribe_price_alert() {
        Serene_Panel_Security::verify_nonce();

        $pid   = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $type  = isset($_POST['alert_type']) ? sanitize_key($_POST['alert_type']) : 'price_drop';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

        if (!$pid) {
            wp_send_json_error(['message' => 'شناسه محصول نامعتبر است.']);
        }

        if (!Serene_Panel_Auth::is_valid_iran_phone($phone)) {
            wp_send_json_error(['message' => 'شماره موبایل نامعتبر است. (مثال: 09123456789)']);
        }

        $phone = Serene_Panel_Auth::normalize_phone($phone);
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;

        $res = Palette_Panel_Price_Alert::add_alert($user_id, $pid, $type, $phone);
        if ($res) {
            wp_send_json_success(['message' => 'درخواست اطلاع‌رسانی شما با موفقیت ثبت شد!']);
        } else {
            wp_send_json_error(['message' => 'خطا در ثبت درخواست اطلاع‌رسانی.']);
        }
    }

    // 18. Save Dynamic Loyalty Tiers
    public function ajax_admin_save_loyalty_tiers() {
        Serene_Panel_Security::verify_nonce();
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'دسترسی غیرمجاز']);

        $tiers_json = isset($_POST['tiers']) ? stripslashes($_POST['tiers']) : '[]';
        $tiers = json_decode($tiers_json, true);

        if (!is_array($tiers)) {
            wp_send_json_error(['message' => 'داده‌های ارسالی نامعتبر است.']);
        }

        Palette_Panel_Loyalty_Tiers::save_tiers($tiers);
        wp_send_json_success(['message' => 'سطوح و قوانین باشگاه مشتریان با موفقیت ذخیره شد.']);
    }

    // 19. Delete Lucky Wheel Coupon Record
    public function ajax_admin_delete_wheel_coupon() {
        Serene_Panel_Security::verify_nonce();
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'دسترسی غیرمجاز']);

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if (!$id) wp_send_json_error(['message' => 'شناسه نامعتبر است.']);

        global $wpdb;
        $table = $wpdb->prefix . 'serene_lucky_wheel_spins';

        $row = $wpdb->get_row($wpdb->prepare("SELECT coupon_code FROM $table WHERE id = %d", $id));
        if ($row && !empty($row->coupon_code) && class_exists('WC_Coupon')) {
            $coupon_id = wc_get_coupon_id_by_code($row->coupon_code);
            if ($coupon_id) {
                wp_delete_post($coupon_id, true);
            }
        }

        $deleted = $wpdb->delete($table, ['id' => $id]);
        if ($deleted) {
            wp_send_json_success(['message' => 'کد تخفیف و رکورد چرخش با موفقیت حذف شد.']);
        } else {
            wp_send_json_error(['message' => 'خطا در حذف رکورد.']);
        }
    }

    // 20. Clear Expired Lucky Wheel Coupons
    public function ajax_admin_clear_expired_wheel_coupons() {
        Serene_Panel_Security::verify_nonce();
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'دسترسی غیرمجاز']);

        global $wpdb;
        $table = $wpdb->prefix . 'serene_lucky_wheel_spins';
        $now = current_time('mysql');

        $expired_rows = $wpdb->get_results($wpdb->prepare("SELECT id, coupon_code FROM $table WHERE expires_at IS NOT NULL AND expires_at < %s", $now));
        $count = 0;
        foreach ($expired_rows as $row) {
            if (!empty($row->coupon_code) && class_exists('WC_Coupon')) {
                $coupon_id = wc_get_coupon_id_by_code($row->coupon_code);
                if ($coupon_id) {
                    wp_delete_post($coupon_id, true);
                }
            }
            $wpdb->delete($table, ['id' => $row->id]);
            $count++;
        }

        wp_send_json_success(['message' => "تعداد {$count} کد تخفیف منقضی‌شده با موفقیت پاکسازی شد.", 'count' => $count]);
    }
}
