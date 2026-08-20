<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Autoloader {
    public static function register() {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    public static function autoload($class) {
        if (strpos($class, 'Serene_Panel_') !== 0 && 
            strpos($class, 'Palette_Panel_') !== 0 && 
            strpos($class, 'Palette_Gateway_') !== 0 && 
            strpos($class, 'Palette_Woo_') !== 0 && 
            strpos($class, 'WC_Gateway_') !== 0) {
            return;
        }

        $map = [
            // Core
            'Serene_Panel_Activator'         => 'includes/class-activator.php',
            'Palette_Panel_Activator'        => 'includes/class-activator.php',
            'Serene_Panel_Assets'            => 'includes/class-assets.php',
            'Palette_Panel_Assets'           => 'includes/class-assets.php',
            'Serene_Panel_Ajax_Handler'      => 'includes/class-ajax-handler.php',
            'Palette_Panel_Ajax_Handler'     => 'includes/class-ajax-handler.php',

            // Security
            'Serene_Panel_Security'          => 'includes/security/class-security-rules.php',
            'Palette_Panel_Security'         => 'includes/security/class-security-rules.php',
            'Serene_Panel_Rate_Limiter'      => 'includes/security/class-rate-limiter.php',
            'Palette_Panel_Rate_Limiter'     => 'includes/security/class-rate-limiter.php',
            'Serene_Panel_SMS_Logger'        => 'includes/security/class-sms-logger.php',
            'Palette_Panel_SMS_Logger'       => 'includes/security/class-sms-logger.php',
            'Serene_Panel_System_Logger'     => 'includes/security/class-system-logger.php',
            'Palette_Panel_System_Logger'    => 'includes/security/class-system-logger.php',

            // Auth
            'Serene_Panel_Auth'              => 'includes/auth/class-auth-core.php',
            'Palette_Panel_Auth'             => 'includes/auth/class-auth-core.php',
            'Serene_Panel_OTP_Service'       => 'includes/auth/class-otp-service.php',
            'Palette_Panel_OTP_Service'      => 'includes/auth/class-otp-service.php',
            'Serene_Panel_WebAuthn'          => 'includes/auth/class-webauthn.php',
            'Palette_Panel_WebAuthn'         => 'includes/auth/class-webauthn.php',
            'Serene_Panel_Two_Factor'        => 'includes/auth/class-two-factor.php',
            'Palette_Panel_Two_Factor'       => 'includes/auth/class-two-factor.php',
            'Serene_Panel_Google_OAuth'      => 'includes/auth/class-google-oauth.php',
            'Palette_Panel_Google_OAuth'     => 'includes/auth/class-google-oauth.php',

            // Dashboard
            'Serene_Panel_Dashboard'         => 'includes/dashboard/class-dashboard.php',
            'Palette_Panel_Dashboard'        => 'includes/dashboard/class-dashboard.php',
            'Serene_Panel_Menu_Builder'      => 'includes/dashboard/class-menu-builder.php',
            'Palette_Panel_Menu_Builder'     => 'includes/dashboard/class-menu-builder.php',
            'Serene_Panel_Form_Builder'      => 'includes/dashboard/class-form-builder.php',
            'Palette_Panel_Form_Builder'     => 'includes/dashboard/class-form-builder.php',
            'Serene_Panel_Notifications'     => 'includes/dashboard/class-notifications.php',
            'Palette_Panel_Notifications'    => 'includes/dashboard/class-notifications.php',
            'Serene_Panel_Profile'           => 'includes/dashboard/class-profile.php',
            'Palette_Panel_Profile'          => 'includes/dashboard/class-profile.php',

            // Commerce
            'Serene_Panel_Orders'            => 'includes/commerce/class-orders.php',
            'Palette_Panel_Orders'           => 'includes/commerce/class-orders.php',
            'Serene_Panel_Quick_Checkout'    => 'includes/commerce/class-quick-checkout.php',
            'Palette_Panel_Quick_Checkout'   => 'includes/commerce/class-quick-checkout.php',
            'Serene_Panel_Card_To_Card'      => 'includes/commerce/class-card-to-card.php',
            'Palette_Panel_Card_To_Card'     => 'includes/commerce/class-card-to-card.php',
            'Palette_Gateway_Card_To_Card'   => 'includes/commerce/class-card-to-card.php',
            'WC_Gateway_Serene_Card_To_Card' => 'includes/commerce/class-card-to-card.php',
            'Serene_Gateway_Card_To_Card'    => 'includes/commerce/class-card-to-card.php',
            'Serene_Panel_Wallet'            => 'includes/commerce/class-wallet.php',
            'Palette_Panel_Wallet'           => 'includes/commerce/class-wallet.php',
            'WC_Gateway_Serene_Wallet'       => 'includes/commerce/class-wallet.php',
            'WC_Gateway_Palette_Wallet'      => 'includes/commerce/class-wallet.php',

            // Engagement
            'Serene_Panel_Tickets'           => 'includes/engagement/class-tickets.php',
            'Palette_Panel_Tickets'          => 'includes/engagement/class-tickets.php',
            'Serene_Panel_Live_Chat'         => 'includes/engagement/class-live-chat.php',
            'Palette_Panel_Live_Chat'        => 'includes/engagement/class-live-chat.php',
            'Serene_Panel_Lucky_Wheel'       => 'includes/engagement/class-lucky-wheel.php',
            'Palette_Panel_Lucky_Wheel'      => 'includes/engagement/class-lucky-wheel.php',
            'Serene_Panel_Smart_Reviews'     => 'includes/engagement/class-smart-reviews.php',
            'Palette_Panel_Smart_Reviews'    => 'includes/engagement/class-smart-reviews.php',

            // Advanced
            'Serene_Panel_PWA'               => 'includes/advanced/class-pwa.php',
            'Palette_Panel_PWA'              => 'includes/advanced/class-pwa.php',
            'Serene_Panel_Shahkar'           => 'includes/advanced/class-shahkar.php',
            'Palette_Panel_Shahkar'          => 'includes/advanced/class-shahkar.php',
            'Serene_Panel_Affiliate'         => 'includes/advanced/class-affiliate.php',
            'Palette_Panel_Affiliate'        => 'includes/advanced/class-affiliate.php',
            'Serene_Panel_Post_Tracker'      => 'includes/advanced/class-post-tracker.php',
            'Palette_Panel_Post_Tracker'     => 'includes/advanced/class-post-tracker.php',
            'Serene_Panel_PDF_Invoice'       => 'includes/advanced/class-pdf-invoice.php',
            'Palette_Panel_PDF_Invoice'      => 'includes/advanced/class-pdf-invoice.php',
            'Serene_Panel_RMA'               => 'includes/advanced/class-rma.php',
            'Palette_Panel_RMA'              => 'includes/advanced/class-rma.php',
            'Serene_Panel_Loyalty_Tiers'     => 'includes/advanced/class-loyalty-tiers.php',
            'Palette_Panel_Loyalty_Tiers'    => 'includes/advanced/class-loyalty-tiers.php',
            'Serene_Panel_Price_Alert'       => 'includes/advanced/class-price-alert.php',
            'Palette_Panel_Price_Alert'      => 'includes/advanced/class-price-alert.php',
            'Serene_Panel_Session_Manager'   => 'includes/advanced/class-session-manager.php',
            'Palette_Panel_Session_Manager'  => 'includes/advanced/class-session-manager.php',
            'Serene_Panel_License_Manager'   => 'includes/advanced/class-license-manager.php',
            'Palette_Panel_License_Manager'  => 'includes/advanced/class-license-manager.php',

            // Admin
            'Serene_Panel_Admin_Settings'    => 'includes/admin/class-admin-settings.php',
            'Palette_Panel_Admin_Settings'   => 'includes/admin/class-admin-settings.php',
        ];

        if (isset($map[$class])) {
            $file = SERENE_PANEL_PATH . $map[$class];
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}

Serene_Panel_Autoloader::register();
