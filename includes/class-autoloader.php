<?php
if (!defined('ABSPATH')) {
    exit;
}

class Palette_Panel_Autoloader {
    public static function register() {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    public static function autoload($class) {
        if (strpos($class, 'Palette_Panel_') !== 0 && 
            strpos($class, 'Palette_Woo_') !== 0 && 
            strpos($class, 'Serene_Panel_') !== 0) {
            return;
        }

        $map = [
            // Core
            'Palette_Panel_Activator'        => 'includes/class-activator.php',
            'Palette_Panel_Assets'           => 'includes/class-assets.php',
            'Palette_Panel_Ajax_Handler'     => 'includes/class-ajax-handler.php',

            // Security
            'Palette_Panel_Security'         => 'includes/security/class-security-rules.php',
            'Palette_Panel_Rate_Limiter'     => 'includes/security/class-rate-limiter.php',
            'Palette_Panel_SMS_Logger'       => 'includes/security/class-sms-logger.php',
            'Palette_Panel_System_Logger'    => 'includes/security/class-system-logger.php',

            // Auth
            'Palette_Panel_Auth'             => 'includes/auth/class-auth-core.php',
            'Palette_Panel_OTP_Service'      => 'includes/auth/class-otp-service.php',
            'Palette_Panel_Google_OAuth'     => 'includes/auth/class-google-oauth.php',

            // Dashboard
            'Palette_Panel_Dashboard'        => 'includes/dashboard/class-dashboard.php',
            'Palette_Panel_Menu_Builder'     => 'includes/dashboard/class-menu-builder.php',
            'Palette_Panel_Form_Builder'     => 'includes/dashboard/class-form-builder.php',
            'Palette_Panel_Notifications'    => 'includes/dashboard/class-notifications.php',
            'Palette_Panel_Profile'          => 'includes/dashboard/class-profile.php',

            // Engagement
            'Palette_Panel_Tickets'          => 'includes/engagement/class-tickets.php',
            'Palette_Panel_Smart_Reviews'    => 'includes/engagement/class-smart-reviews.php',
            'Palette_Panel_Live_Chat'        => 'includes/engagement/class-live-chat.php',

            // Commerce
            'Palette_Panel_Orders'           => 'includes/commerce/class-orders.php',
            'Palette_Panel_Wallet'           => 'includes/commerce/class-wallet.php',
            'Palette_Panel_Quick_Checkout'   => 'includes/commerce/class-quick-checkout.php',

            // Admin
            'Palette_Panel_Admin_Settings'   => 'includes/admin/class-admin-settings.php',

            // Gateways
            'Palette_Gateway_Kavenegar'      => 'includes/auth/gateways/class-kavenegar.php',
            'Palette_Gateway_Melipayamak'    => 'includes/auth/gateways/class-melipayamak.php',
            'Palette_Gateway_SMSIR'          => 'includes/auth/gateways/class-smsir.php',
            'Palette_Gateway_NikSMS'         => 'includes/auth/gateways/class-niksms.php',
            'Palette_Gateway_Email_OTP'      => 'includes/auth/gateways/class-email-otp.php',
            'Palette_Gateway_Telegram_Bot'   => 'includes/auth/gateways/class-telegram-bot.php',
            'Palette_Gateway_Bale_Bot'       => 'includes/auth/gateways/class-bale-bot.php',
        ];

        // Also alias Serene_ names for internal backward compatibility
        $normalized_class = str_replace('Serene_Panel_', 'Palette_Panel_', $class);
        $normalized_class = str_replace('Serene_Gateway_', 'Palette_Gateway_', $normalized_class);

        if (isset($map[$normalized_class])) {
            $file = PALETTE_PANEL_PATH . $map[$normalized_class];
            if (file_exists($file)) {
                require_once $file;
                
                $serene_class = str_replace('Palette_Panel_', 'Serene_Panel_', $normalized_class);
                $serene_class = str_replace('Palette_Gateway_', 'Serene_Gateway_', $serene_class);

                if (class_exists($serene_class, false) && !class_exists($normalized_class, false)) {
                    class_alias($serene_class, $normalized_class);
                }
                if (class_exists($normalized_class, false) && !class_exists($serene_class, false)) {
                    class_alias($normalized_class, $serene_class);
                }
                return;
            }
        }

        // Safe Fallback Stub for removed Pro features in Lite edition (Prevents Fatal Errors)
        if (!class_exists($normalized_class, false) && !class_exists($class, false)) {
            eval("
                class {$normalized_class} {
                    private static \$inst = null;
                    public static function get_instance() { if (null === self::\$inst) self::\$inst = new self(); return self::\$inst; }
                    public static function instance() { return self::get_instance(); }
                    public static function __callStatic(\$name, \$arguments) { return []; }
                    public function __call(\$name, \$arguments) { return []; }
                }
            ");
            if ($class !== $normalized_class && !class_exists($class, false)) {
                class_alias($normalized_class, $class);
            }
        }
    }
}

if (!class_exists('Serene_Panel_Autoloader', false)) {
    class_alias('Palette_Panel_Autoloader', 'Serene_Panel_Autoloader');
}
