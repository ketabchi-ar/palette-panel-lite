<?php
if (!defined('ABSPATH')) {
    exit;
}

function palette_init_c2c_gateway_class() {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    if (!class_exists('Palette_Gateway_Card_To_Card')) {
        class Palette_Gateway_Card_To_Card extends WC_Payment_Gateway {
            public function __construct() {
                $this->id                 = 'palette_c2c';
                $this->icon               = '';
                $this->has_fields         = true;
                $this->method_title       = 'کارت به کارت خودکار و هوشمند (پالت پنل)';
                $this->method_description = 'پرداخت مستقیم کارت به کارت با قابلیت آپلود فیش، پیگیری و تطبیق خودکار پیامک بانک توسط آژانس دیجیتال پالت';
                $this->supports           = ['products'];

                $this->init_form_fields();
                $this->init_settings();

                $opt = get_option('serene_panel_options', []);
                $this->title       = !empty($opt['c2c_title']) ? $opt['c2c_title'] : $this->get_option('title', 'کارت به کارت خودکار و هوشمند');
                $this->description = !empty($opt['c2c_desc']) ? $opt['c2c_desc'] : $this->get_option('description', 'انتقال وجه به شماره کارت فروشگاه و ثبت فیش واریزی');
                $this->enabled     = (!empty($opt['enable_c2c']) || $this->get_option('enabled') === 'yes' || !isset($opt['enable_c2c'])) ? 'yes' : 'no';

                add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
            }

            public function init_form_fields() {
                $opt = get_option('serene_panel_options', []);
                $this->form_fields = [
                    'enabled' => [
                        'title'   => 'فعالسازی / غیرفعالسازی',
                        'type'    => 'checkbox',
                        'label'   => 'فعالسازی درگاه پرداخت کارت به کارت پالت پنل',
                        'default' => 'yes',
                    ],
                    'title' => [
                        'title'       => 'عنوان درگاه در تسویه‌حساب',
                        'type'        => 'text',
                        'description' => 'عنوانی که خریدار در مرحله پرداخت تسویه‌حساب مشاهده می‌کند.',
                        'default'     => 'کارت به کارت خودکار و هوشمند',
                    ],
                    'description' => [
                        'title'       => 'توضیحات درگاه',
                        'type'        => 'textarea',
                        'description' => 'توضیحات راهنما برای خریدار.',
                        'default'     => 'مبلغ سفارش را به شماره کارت فروشگاه واریز فرموده و رسید یا کد رهگیری را ثبت نمایید.',
                    ],
                    'card_number' => [
                        'title'       => 'شماره کارت بانکی',
                        'type'        => 'text',
                        'default'     => $opt['c2c_card_number'] ?? '6037991122334455',
                    ],
                    'card_holder' => [
                        'title'       => 'نام صاحب حساب',
                        'type'        => 'text',
                        'default'     => $opt['c2c_card_holder'] ?? 'فروشگاه پالت',
                    ],
                    'bank_name' => [
                        'title'       => 'نام بانک',
                        'type'        => 'text',
                        'default'     => $opt['c2c_bank_name'] ?? 'ملی',
                    ],
                    'sheba_number' => [
                        'title'       => 'شماره شبا (اختیاری)',
                        'type'        => 'text',
                        'default'     => $opt['c2c_sheba_number'] ?? '',
                    ],
                ];
            }

            public function is_available() {
                $opt = get_option('serene_panel_options', []);
                $is_enabled = (!empty($opt['enable_c2c']) || $this->get_option('enabled') === 'yes' || !isset($opt['enable_c2c']));
                
                if (isset($opt['enable_c2c']) && empty($opt['enable_c2c']) && $this->get_option('enabled') !== 'yes') {
                    $is_enabled = false;
                }

                if (!$is_enabled) {
                    return false;
                }

                return apply_filters('woocommerce_gateway_is_available', true, $this);
            }

            public function payment_fields() {
                $options = get_option('serene_panel_options', []);
                $card   = !empty($options['c2c_card_number']) ? $options['c2c_card_number'] : $this->get_option('card_number', '6037991122334455');
                $holder = !empty($options['c2c_card_holder']) ? $options['c2c_card_holder'] : $this->get_option('card_holder', 'فروشگاه پالت');
                $bank   = !empty($options['c2c_bank_name']) ? $options['c2c_bank_name'] : $this->get_option('bank_name', 'ملی');
                $sheba  = !empty($options['c2c_sheba_number']) ? $options['c2c_sheba_number'] : $this->get_option('sheba_number', '');

                if (!empty($this->description)) {
                    echo '<p style="font-size:13px; color:#475569; margin-bottom:12px; line-height: 1.6;">' . wpautop(wptexturize($this->description)) . '</p>';
                }
                ?>
                <div class="palette-c2c-checkout-card" style="background: linear-gradient(135deg, #1e293b, #334155); color: #fff; padding: 22px; border-radius: 20px; margin: 14px 0; font-family: inherit; direction: rtl; box-shadow: 0 10px 25px rgba(0,0,0,0.12); border: 1px solid rgba(255,255,255,0.1);">
                    <div style="display:flex; justify-content:space-between; font-size:12px; color:#cbd5e1; margin-bottom:14px;">
                        <span style="font-weight:bold;">بانک <?php echo esc_html($bank); ?></span>
                        <span>صاحب حساب: <?php echo esc_html($holder); ?></span>
                    </div>
                    <div style="font-family:monospace; font-size:19px; font-weight:bold; letter-spacing:3px; text-align:center; margin-bottom:10px; color:#f8fafc;" dir="ltr">
                        <?php echo esc_html($card); ?>
                    </div>
                    <?php if ($sheba): ?>
                        <div style="font-size:12px; color:#94a3b8; text-align:center; font-family: monospace;" dir="ltr">
                            IR<?php echo esc_html($sheba); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <p style="font-size:12px; color:#64748b; margin-top:8px; line-height: 1.5;">
                    💡 پس از ثبت سفارش، فیش واریزی یا شماره پیگیری را در صفحه بعد ثبت نمایید تا سفارش شما بررسی و تکمیل گردد.
                </p>
                <?php
            }

            public function process_payment($order_id) {
                $order = wc_get_order($order_id);

                if (function_exists('wc_reduce_stock_levels')) {
                    wc_reduce_stock_levels($order_id);
                }

                $order->update_status('on-hold', 'در انتظار واریز کارت به کارت و ثبت فیش توسط مشتری.');
                WC()->cart->empty_cart();

                return [
                    'result'   => 'success',
                    'redirect' => $this->get_return_url($order),
                ];
            }
        }
    }

    // Alias for legacy configurations
    if (!class_exists('WC_Gateway_Serene_Card_To_Card')) {
        class WC_Gateway_Serene_Card_To_Card extends Palette_Gateway_Card_To_Card {}
    }
    if (!class_exists('Serene_Gateway_Card_To_Card')) {
        class Serene_Gateway_Card_To_Card extends Palette_Gateway_Card_To_Card {}
    }
}

add_action('plugins_loaded', 'palette_init_c2c_gateway_class', 0);
if (class_exists('WC_Payment_Gateway')) {
    palette_init_c2c_gateway_class();
}

add_filter('woocommerce_payment_gateways', 'palette_register_c2c_payment_gateway');
function palette_register_c2c_payment_gateway($gateways) {
    palette_init_c2c_gateway_class();
    if (class_exists('Palette_Gateway_Card_To_Card') && !in_array('Palette_Gateway_Card_To_Card', $gateways, true)) {
        $gateways[] = 'Palette_Gateway_Card_To_Card';
    }
    return $gateways;
}

// 3. Manager & Hook Handler Class
class Palette_Panel_Card_To_Card {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('rest_api_init', [$this, 'register_bank_sms_endpoint']);
        add_action('add_meta_boxes', [$this, 'register_admin_c2c_meta_box']);
        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_c2c_random_cents_fee']);
        add_action('woocommerce_thankyou_palette_c2c', [$this, 'render_thankyou_c2c_box'], 10, 1);
        add_action('woocommerce_thankyou_serene_c2c', [$this, 'render_thankyou_c2c_box'], 10, 1);
        add_action('woocommerce_thankyou', [$this, 'render_thankyou_c2c_fallback'], 10, 1);
    }

    public function apply_c2c_random_cents_fee($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;
        $options = get_option('serene_panel_options', []);
        if (empty($options['enable_c2c_random_cents'])) return;

        $chosen_gateway = WC()->session ? WC()->session->get('chosen_payment_method') : '';
        if (!in_array($chosen_gateway, ['palette_c2c', 'serene_c2c', 'c2c'], true)) return;

        $digits = intval($options['c2c_random_digits'] ?? 2);
        $min = ($digits === 1) ? 1 : (($digits === 3) ? 101 : 11);
        $max = ($digits === 1) ? 9 : (($digits === 3) ? 999 : 99);

        $fee_amount = WC()->session->get('c2c_random_modifier_fee');
        if (!$fee_amount) {
            $fee_amount = wp_rand($min, $max);
            WC()->session->set('c2c_random_modifier_fee', $fee_amount);
        }

        $fee_title = !empty($options['c2c_random_fee_title']) ? $options['c2c_random_fee_title'] : 'تطبیق هوشمند بانکی (شناسه رندوم واریز)';
        $cart->add_fee($fee_title, $fee_amount, false);
    }

    public static function get_builtin_banks() {
        return [
            ['name' => 'بلوبانک (BluBank)', 'icon' => 'account_balance', 'badge' => 'فرمت جدید و کلاسیک'],
            ['name' => 'بانک ملت', 'icon' => 'account_balance', 'badge' => 'واریز و کد پیگیری'],
            ['name' => 'بانک ملی', 'icon' => 'account_balance', 'badge' => 'واریز و انتقال حساب'],
            ['name' => 'بانک سامان', 'icon' => 'account_balance', 'badge' => 'پیامک تومانی و ریالی'],
            ['name' => 'بانک پاسارگاد', 'icon' => 'account_balance', 'badge' => 'واریز به سپرده و کارت'],
            ['name' => 'بانک رسالت', 'icon' => 'account_balance', 'badge' => 'انتقال شتابی و داخلی'],
            ['name' => 'بانک تجارت', 'icon' => 'account_balance', 'badge' => 'واریز شناسه و پایا'],
            ['name' => 'بانک صادرات', 'icon' => 'account_balance', 'badge' => 'سپرده سپهر و کارت'],
            ['name' => 'بانک سپه', 'icon' => 'account_balance', 'badge' => 'سامانه امید و نوین'],
            ['name' => 'بانک آینده', 'icon' => 'account_balance', 'badge' => 'واریز و انتقال آبان'],
            ['name' => 'بانک شهر', 'icon' => 'account_balance', 'badge' => 'واریز و شارژ حساب'],
            ['name' => 'بانک پارسیان', 'icon' => 'account_balance', 'badge' => 'پیامک هوشمند'],
            ['name' => 'بانک رفاه کارگران', 'icon' => 'account_balance', 'badge' => 'پایا و ساتنا'],
            ['name' => 'بانک کشاورزی', 'icon' => 'account_balance', 'badge' => 'سامانه مهر'],
            ['name' => 'بانک مسکن', 'icon' => 'account_balance', 'badge' => 'واریز اقساط و کارت'],
            ['name' => 'بانک سینا', 'icon' => 'account_balance', 'badge' => 'واریز و ساتنا'],
            ['name' => 'شبکه شتاب سراسری', 'icon' => 'credit_card', 'badge' => 'الگوی عمومی هوشمند']
        ];
    }

    public function register_bank_sms_endpoint() {
        register_rest_route('palette/v1', '/bank-sms-webhook', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_bank_sms_webhook'],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route('palette/v1', '/bank-sms', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_bank_sms_webhook'],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route('palette/v1', '/pending-card-orders', [
            'methods'             => ['GET', 'POST'],
            'callback'            => [$this, 'handle_pending_card_orders'],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route('serene/v1', '/bank-sms-webhook', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_bank_sms_webhook'],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route('serene/v1', '/bank-sms', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_bank_sms_webhook'],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route('serene/v1', '/pending-card-orders', [
            'methods'             => ['GET', 'POST'],
            'callback'            => [$this, 'handle_pending_card_orders'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handle_pending_card_orders($request) {
        $secret = $request->get_param('secret') ?: ($request->get_param('key') ?: $request->get_header('X-Palette-Secret'));
        $options = get_option('palette_panel_options', []);
        if (empty($options)) {
            $options = get_option('serene_panel_options', []);
        }

        if (!empty($options['c2c_webhook_secret']) && $secret !== $options['c2c_webhook_secret']) {
            return new WP_REST_Response(['status' => 'error', 'message' => 'Unauthorized: Invalid secret key'], 403);
        }

        if (!function_exists('wc_get_orders')) {
            return new WP_REST_Response(['status' => 'error', 'message' => 'WooCommerce is not active'], 500);
        }

        $orders = wc_get_orders([
            'status'  => ['on-hold', 'pending'],
            'limit'   => 50,
            'orderby' => 'date',
            'order'   => 'DESC',
        ]);

        $list = [];
        foreach ($orders as $order) {
            $order_id = $order->get_id();
            $receipt_data = $order->get_meta('_palette_c2c_receipt') ?: $order->get_meta('_serene_c2c_receipt');
            
            $sender_card = '';
            $tracking_code = '';
            $receipt_url = '';
            $created_at = '';
            
            if ($receipt_data) {
                if (is_string($receipt_data)) {
                    $receipt_data = json_decode($receipt_data, true);
                }
                if (is_array($receipt_data)) {
                    $sender_card = $receipt_data['sender_card'] ?? '';
                    $tracking_code = $receipt_data['tracking_code'] ?? '';
                    $receipt_url = $receipt_data['receipt_url'] ?? '';
                    $created_at = $receipt_data['created_at'] ?? '';
                }
            }

            $list[] = [
                'id'             => $order_id,
                'order_number'   => $order->get_order_number(),
                'status'         => $order->get_status(),
                'status_name'    => wc_get_order_status_name($order->get_status()),
                'payment_method' => $order->get_payment_method(),
                'total'          => (float) $order->get_total(),
                'currency'       => $order->get_currency(),
                'customer_name'  => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()) ?: 'مشتری مهمان',
                'customer_phone' => $order->get_billing_phone(),
                'date_created'   => $order->get_date_created() ? $order->get_date_created()->date('Y-m-d H:i:s') : '',
                'sender_card'    => $sender_card,
                'tracking_code'  => $tracking_code,
                'receipt_url'    => $receipt_url,
                'has_receipt'    => !empty($receipt_url),
            ];
        }

        return new WP_REST_Response([
            'status'         => 'success',
            'store_name'     => get_bloginfo('name'),
            'total_pending'  => count($list),
            'orders'         => $list
        ], 200);
    }

    public function handle_bank_sms_webhook($request) {
        $body = $request->get_json_params() ?: $request->get_params();
        $raw_sms = isset($body['sms']) ? sanitize_textarea_field($body['sms']) : (isset($body['text']) ? sanitize_textarea_field($body['text']) : '');
        $secret  = isset($body['secret']) ? sanitize_text_field($body['secret']) : (isset($body['key']) ? sanitize_text_field($body['key']) : '');

        $options = get_option('serene_panel_options', []);
        if (!empty($options['c2c_webhook_secret']) && $secret !== $options['c2c_webhook_secret']) {
            if (class_exists('Serene_Panel_System_Logger')) {
                Serene_Panel_System_Logger::warning('WEBHOOK', 'درخواست وب‌هوک پیامک با کلید امنیتی نامعتبر رد شد.');
            }
            return new WP_REST_Response(['status' => 'error', 'message' => 'Unauthorized secret key'], 403);
        }

        if (empty($raw_sms)) {
            return new WP_REST_Response(['status' => 'error', 'message' => 'No SMS text provided in request body'], 400);
        }

        $parsed = self::parse_bank_sms($raw_sms);
        if (!$parsed) {
            if (class_exists('Serene_Panel_System_Logger')) {
                Serene_Panel_System_Logger::info('WEBHOOK', 'پیامک دریافتی به عنوان الگوی واریز بانکی شناسایی نشد: ' . mb_substr($raw_sms, 0, 80));
            }
            return new WP_REST_Response(['status' => 'ignored', 'message' => 'SMS pattern not matched as deposit', 'raw' => $raw_sms], 200);
        }

        $match_result = self::match_and_complete_order($parsed, $raw_sms);

        if (class_exists('Serene_Panel_System_Logger')) {
            if ($match_result) {
                Serene_Panel_System_Logger::success('WEBHOOK', sprintf('پیامک بانک %s پردازش شد و سفارش #%d با موفقیت تایید خودکار گردید.', $parsed['bank'], $match_result['order_id']));
            } else {
                Serene_Panel_System_Logger::info('WEBHOOK', sprintf('پیامک بانک %s تجزیه شد (مبلغ: %s تومان | کد پیگیری: %s) اما سفارش منطبق در حالت انتظار یافت نشد.', $parsed['bank'], number_format($parsed['amount']), $parsed['tracking_code'] ?: 'ندارد'));
            }
        }

        return new WP_REST_Response([
            'status'  => 'success',
            'parsed'  => $parsed,
            'matched' => $match_result,
        ], 200);
    }

    public static function parse_bank_sms($sms) {
        $clean = str_replace([',', '،', 'ي', 'ك'], ['', '', 'ی', 'ک'], $sms);
        
        $farsi = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $latin = ['0','1','2','3','4','5','6','7','8','9'];
        $clean = str_replace($farsi, $latin, $clean);

        $data = [
            'bank'          => 'بانک عضو شتاب',
            'amount'        => 0,
            'tracking_code' => '',
            'raw'           => $sms,
        ];

        // 0. Check custom learned patterns first
        $custom_patterns = get_option('palette_custom_bank_patterns', []);
        if (is_array($custom_patterns) && !empty($custom_patterns)) {
            foreach ($custom_patterns as $p) {
                if (empty($p['regex'])) continue;
                if (@preg_match($p['regex'], $clean, $m)) {
                    $raw_amount = floatval($m[1] ?? 0);
                    $unit = $p['unit'] ?? 'rial';
                    $data['bank'] = !empty($p['bank_name']) ? $p['bank_name'] : 'الگوی سفارشی';
                    $data['amount'] = ($unit === 'toman') ? $raw_amount : ($raw_amount / 10);
                    if (isset($m[2]) && is_numeric($m[2])) {
                        $data['tracking_code'] = $m[2];
                    }
                    if ($data['amount'] > 0) {
                        return $data;
                    }
                }
            }
        }

        // Bank detection
        if (stripos($clean, 'بلو') !== false || stripos($clean, 'blu') !== false) {
            $data['bank'] = 'بلوبانک (BluBank)';
        } elseif (stripos($clean, 'ملت') !== false) {
            $data['bank'] = 'بانک ملت';
        } elseif (stripos($clean, 'ملی') !== false) {
            $data['bank'] = 'بانک ملی';
        } elseif (stripos($clean, 'سامان') !== false) {
            $data['bank'] = 'بانک سامان';
        } elseif (stripos($clean, 'پاسارگاد') !== false) {
            $data['bank'] = 'بانک پاسارگاد';
        } elseif (stripos($clean, 'رسالت') !== false) {
            $data['bank'] = 'بانک رسالت';
        } elseif (stripos($clean, 'صادرات') !== false) {
            $data['bank'] = 'بانک صادرات';
        } elseif (stripos($clean, 'تجارت') !== false) {
            $data['bank'] = 'بانک تجارت';
        } elseif (stripos($clean, 'سپه') !== false) {
            $data['bank'] = 'بانک سپه';
        } elseif (stripos($clean, 'آینده') !== false) {
            $data['bank'] = 'بانک آینده';
        } elseif (stripos($clean, 'شهر') !== false) {
            $data['bank'] = 'بانک شهر';
        }

        // 1. Direct "مبلغ: 5000000 ریال/تومان"
        if (preg_match('/مبلغ\s*[:=]?\s*(\d{4,14})\s*(ریال|تومان)?/us', $clean, $m)) {
            $raw_amount = floatval($m[1]);
            $unit = $m[2] ?? 'ریال';
            $data['amount'] = ($unit === 'تومان') ? $raw_amount : ($raw_amount / 10);
        }
        // 2. BluBank "12168650 ریال به حساب شما نشست"
        elseif (preg_match('/(\d+)\s*(ریال|تومان)?\s*به حساب شما نشست/us', $clean, $m)) {
            $raw_amount = floatval($m[1]);
            $unit = $m[2] ?? 'ریال';
            $data['amount'] = ($unit === 'تومان') ? $raw_amount : ($raw_amount / 10);
        }
        // 3. "واریز/انتقال/افزایش/شارژ (\d+) ریال/تومان"
        elseif (preg_match('/(?:واریز|انتقال|افزایش|شارژ|واریز پول|واریز به|دریافت)\s*[:=]?\s*(\d{4,14})\s*(ریال|تومان)?/us', $clean, $m)) {
            $raw_amount = floatval($m[1]);
            $unit = $m[2] ?? 'ریال';
            $data['amount'] = ($unit === 'تومان') ? $raw_amount : ($raw_amount / 10);
        }
        // 4. "(\d+) (ریال|تومان) (واریز|انتقال) شد"
        elseif (preg_match('/(\d{4,14})\s*(ریال|تومان)\s*(?:واریز|انتقال|افزایش)/us', $clean, $m)) {
            $raw_amount = floatval($m[1]);
            $unit = $m[2] ?? 'ریال';
            $data['amount'] = ($unit === 'تومان') ? $raw_amount : ($raw_amount / 10);
        }
        // 5. Fallback generic deposit pattern
        elseif (preg_match('/(?:واریز|انتقال).*?(\d{4,14})/us', $clean, $m)) {
            $raw_amount = floatval($m[1]);
            $data['amount'] = ($raw_amount > 5000000) ? ($raw_amount / 10) : $raw_amount;
        }

        // Tracking code detection
        if (preg_match('/(?:پیگیری|کد|ارجاع|مرجع|رهگیری|شماره پیگیری|شماره ارجاع|کد پیگیری|کد رهگیری)\s*:?\s*(\d{4,18})/us', $clean, $tm)) {
            $data['tracking_code'] = $tm[1];
        }

        if ($data['amount'] > 0) {
            return $data;
        }

        return false;
    }

    public static function match_and_complete_order($parsed, $raw_sms) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_card_transfers';

        $amount = $parsed['amount'];
        $track  = $parsed['tracking_code'];

        if (!empty($track)) {
            $record = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE tracking_code = %s AND status = 'pending' LIMIT 1",
                $track
            ));
            if ($record) {
                return self::process_matched_record($record, $parsed, $raw_sms);
            }
        }

        $record = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE amount = %f AND status = 'pending' AND created_at >= %s ORDER BY id DESC LIMIT 1",
            $amount, date('Y-m-d H:i:s', time() - 86400)
        ));

        if ($record) {
            return self::process_matched_record($record, $parsed, $raw_sms);
        }

        // Direct matching with WC Orders on-hold
        if (function_exists('wc_get_orders')) {
            $orders = wc_get_orders([
                'status'         => ['on-hold', 'pending'],
                'payment_method' => ['palette_c2c', 'serene_c2c', 'c2c'],
                'limit'          => 5,
            ]);

            foreach ($orders as $order) {
                if (abs(floatval($order->get_total()) - $amount) < 1.0) {
                    $order->payment_complete();
                    $order->add_order_note(sprintf(
                        'پرداخت کارت‌به‌کارت به صورت خودکار و هوشمند از طریق پیامک %s تایید شد. (مبلغ: %s تومان)',
                        $parsed['bank'],
                        number_format($amount)
                    ));
                    return ['order_id' => $order->get_id(), 'status' => 'completed'];
                }
            }
        }

        return false;
    }

    private static function process_matched_record($record, $parsed, $raw_sms) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_card_transfers';

        $wpdb->update($table, [
            'status'     => 'approved',
            'sms_raw'    => $raw_sms,
            'matched_at' => current_time('mysql'),
        ], ['id' => $record->id]);

        $order = wc_get_order($record->order_id);
        if ($order) {
            $order->payment_complete();
            $order->add_order_note(sprintf(
                'پرداخت کارت‌به‌کارت به صورت خودکار از طریق پیامک بانک %s با موفقیت تایید شد. (کد رهگیری: %s)',
                $parsed['bank'],
                $parsed['tracking_code'] ?: 'تطبیق هوشمند مبلغ'
            ));
            return ['order_id' => $record->order_id, 'status' => 'completed'];
        }

        return false;
    }

    public function register_admin_c2c_meta_box() {
        add_meta_box(
            'palette_c2c_order_meta',
            'اطلاعات و وضعیت پرداخت کارت به کارت (پالت پنل)',
            [$this, 'render_admin_c2c_meta_box'],
            'shop_order',
            'side',
            'high'
        );
        add_meta_box(
            'palette_c2c_order_meta_hpos',
            'اطلاعات و وضعیت پرداخت کارت به کارت (پالت پنل)',
            [$this, 'render_admin_c2c_meta_box'],
            'woocommerce_page_wc-orders',
            'side',
            'high'
        );
    }

    public function render_admin_c2c_meta_box($post_or_order) {
        $order_id = is_a($post_or_order, 'WC_Order') ? $post_or_order->get_id() : ($post_or_order->ID ?? 0);
        if (!$order_id) return;

        global $wpdb;
        $table = $wpdb->prefix . 'serene_card_transfers';
        $record = null;
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") === $table) {
            $record = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE order_id = %d ORDER BY id DESC LIMIT 1", $order_id));
        }

        $order = wc_get_order($order_id);
        
        // Extract meta from array or individual keys
        $receipt_data = $order ? ($order->get_meta('_palette_c2c_receipt') ?: $order->get_meta('_serene_c2c_receipt')) : null;
        if (is_string($receipt_data)) {
            $receipt_data = json_decode($receipt_data, true);
        }

        $sender_card = !empty($record->card_number) ? $record->card_number : (!empty($record->sender_card) ? $record->sender_card : '');
        if (!$sender_card && $order) {
            $sender_card = $order->get_meta('_c2c_payer_card') ?: ($order->get_meta('_palette_c2c_sender_card') ?: ($order->get_meta('_serene_c2c_sender_card') ?: ''));
        }
        if (!$sender_card && is_array($receipt_data)) {
            $sender_card = $receipt_data['sender_card'] ?? ($receipt_data['card_number'] ?? '');
        }
        if (!$sender_card) {
            $sender_card = get_post_meta($order_id, '_c2c_payer_card', true);
        }

        $receipt_url = !empty($record->receipt_image) ? $record->receipt_image : (!empty($record->receipt_url) ? $record->receipt_url : '');
        if (!$receipt_url && $order) {
            $receipt_url = $order->get_meta('_c2c_receipt_url') ?: ($order->get_meta('_palette_c2c_receipt_url') ?: ($order->get_meta('_serene_c2c_receipt_url') ?: ''));
        }
        if (!$receipt_url && is_array($receipt_data)) {
            $receipt_url = $receipt_data['receipt_url'] ?? ($receipt_data['receipt_image'] ?? '');
        }
        if (!$receipt_url) {
            $receipt_url = get_post_meta($order_id, '_c2c_receipt_url', true);
        }
        if (!$receipt_url) {
            $att_id = ($order ? $order->get_meta('_c2c_receipt_image_id') : 0) ?: get_post_meta($order_id, '_c2c_receipt_image_id', true);
            if ($att_id) {
                $receipt_url = wp_get_attachment_url($att_id);
            }
        }

        $track_code = !empty($record->tracking_code) ? $record->tracking_code : '';
        if (!$track_code && $order) {
            $track_code = $order->get_meta('_c2c_tracking_code') ?: ($order->get_meta('_palette_c2c_tracking_code') ?: '');
        }
        if (!$track_code && is_array($receipt_data)) {
            $track_code = $receipt_data['tracking_code'] ?? '';
        }
        if (!$track_code) {
            $track_code = get_post_meta($order_id, '_c2c_tracking_code', true);
        }

        $current_status = $record->status ?? (($order ? $order->get_meta('_c2c_status') : '') ?: (get_post_meta($order_id, '_c2c_status', true) ?: 'pending'));
        $amount = $record->amount ?? ($order ? $order->get_total() : 0);

        if (!$record && !$sender_card && !$receipt_url && !$track_code) {
            echo '<p style="font-size:12px; color:#64748b; margin:0; padding:4px 0;">اطلاعات فیش کارت به کارت برای این سفارش ثبت نشده است.</p>';
            return;
        }

        $statuses = [
            'pending'  => ['label' => 'در انتظار بررسی', 'bg' => '#fef3c7', 'text' => '#92400e'],
            'approved' => ['label' => 'تایید شده (خودکار/دستی)', 'bg' => '#dcfce7', 'text' => '#166534'],
            'rejected' => ['label' => 'رد شده / نامعتبر', 'bg' => '#fee2e2', 'text' => '#991b1b'],
        ];
        $st = $statuses[$current_status] ?? ['label' => $current_status, 'bg' => '#f1f5f9', 'text' => '#475569'];
        ?>
        <div style="font-size:12px; line-height:1.9; direction:rtl; text-align:right; font-family:inherit;">
            <div style="margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:bold; color:#475569;">وضعیت فیش:</span>
                <span style="display:inline-block; padding:3px 10px; border-radius:14px; font-weight:bold; font-size:11px; background:<?php echo $st['bg']; ?>; color:<?php echo $st['text']; ?>;">
                    <?php echo esc_html($st['label']); ?>
                </span>
            </div>
            <div><strong>مبلغ واریزی:</strong> <span style="font-weight:bold; color:#0f172a;"><?php echo number_format($amount); ?> تومان</span></div>
            <div><strong>شماره کارت واریزکننده:</strong> <code style="background:#f1f5f9; padding:2px 6px; border-radius:6px; font-family:monospace;"><?php echo esc_html($sender_card ?: '-'); ?></code></div>
            <div><strong>کد رهگیری / ارجاع:</strong> <code style="background:#f1f5f9; padding:2px 6px; border-radius:6px; font-family:monospace;"><?php echo esc_html($track_code ?: '-'); ?></code></div>
            <?php if (!empty($record->created_at)): ?>
                <div><strong>تاریخ ثبت:</strong> <?php echo esc_html(date_i18n('Y/m/d H:i', strtotime($record->created_at))); ?></div>
            <?php endif; ?>
            
            <?php if ($receipt_url): ?>
                <div style="margin-top:12px; padding-top:10px; border-top:1px dashed #e2e8f0;">
                    <div style="font-weight:bold; margin-bottom:6px; color:#334155; display:flex; justify-content:space-between; align-items:center;">
                        <span>تصویر فیش ارسالی کاربر:</span>
                        <a href="<?php echo esc_url($receipt_url); ?>" target="_blank" style="text-decoration:none; font-size:11px; color:#4c5e8b; font-weight:bold;">باز کردن در تب جدید ↗</a>
                    </div>
                    <div style="position:relative; background:#f8fafc; border:1px solid #cbd5e1; border-radius:12px; overflow:hidden; text-align:center; padding:4px;">
                        <a href="<?php echo esc_url($receipt_url); ?>" target="_blank" title="مشاهده تصویر بزرگ فیش">
                            <img src="<?php echo esc_url($receipt_url); ?>" alt="تصویر فیش کارت به کارت" style="max-width:100%; max-height:220px; border-radius:8px; display:block; margin:0 auto; object-fit:contain; cursor:zoom-in;">
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div style="margin-top:8px; padding:6px 10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; font-size:11px; color:#64748b;">
                    ✓ ثبت شده به صورت متنی با کد رهگیری (بدون پیوست تصویر)
                </div>
            <?php endif; ?>

            <?php if ($current_status === 'pending'): ?>
                <div style="margin-top:14px; display:flex; gap:8px;">
                    <button type="button" class="button button-primary" style="flex:1; border-radius:10px; height:34px; font-weight:bold; font-size:11px; background:#166534; border-color:#166534;" onclick="openPaletteC2CModal(<?php echo $order_id; ?>, 'approve')">✓ تایید فیش و تکمیل</button>
                    <button type="button" class="button" style="flex:1; border-radius:10px; height:34px; font-weight:bold; font-size:11px; color:#991b1b; border-color:#fca5a5; background:#fff;" onclick="openPaletteC2CModal(<?php echo $order_id; ?>, 'reject')">✕ رد فیش</button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Palette Soft UI Modal for Manual Review -->
        <div id="palette-c2c-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px); z-index:999999; align-items:center; justify-content:center;">
            <div style="background:#ffffff; border-radius:24px; padding:28px; max-width:420px; width:92%; box-shadow:0 25px 60px rgba(0,0,0,0.3); text-align:center; font-family:inherit; direction:rtl;">
                <div id="c2c-modal-icon-wrap" style="width:54px; height:54px; border-radius:18px; margin:0 auto 14px; display:flex; align-items:center; justify-content:center; font-size:26px;"></div>
                <h4 id="c2c-modal-header" style="margin:0 0 8px; font-size:16px; font-weight:800; color:#0f172a;"></h4>
                <p id="c2c-modal-body" style="margin:0 0 22px; font-size:12px; color:#64748b; line-height:1.7;"></p>
                
                <div style="display:flex; gap:10px; justify-content:center;">
                    <button type="button" id="c2c-modal-submit-btn" style="border:none; border-radius:14px; padding:10px 22px; font-size:12px; font-weight:bold; color:#fff; cursor:pointer; transition:all 0.2s;"></button>
                    <button type="button" onclick="closePaletteC2CModal()" style="border:1px solid #cbd5e1; background:#f8fafc; border-radius:14px; padding:10px 18px; font-size:12px; font-weight:bold; color:#475569; cursor:pointer;">انصراف</button>
                </div>
            </div>
        </div>

        <script>
        let currentC2COrderId = null;
        let currentC2CAction = null;

        function openPaletteC2CModal(orderId, action) {
            currentC2COrderId = orderId;
            currentC2CAction = action;
            const overlay = document.getElementById('palette-c2c-modal-overlay');
            const iconWrap = document.getElementById('c2c-modal-icon-wrap');
            const header = document.getElementById('c2c-modal-header');
            const body = document.getElementById('c2c-modal-body');
            const btn = document.getElementById('c2c-modal-submit-btn');

            if (action === 'approve') {
                iconWrap.style.background = '#dcfce7';
                iconWrap.style.color = '#166534';
                iconWrap.innerHTML = '✓';
                header.innerText = 'تایید فیش و تکمیل خودکار سفارش';
                body.innerText = 'با تایید فیش، وضعیت سفارش به در حال انجام / تکمیل‌شده تغییر یافته و پیامک تایید برای کاربر ارسال می‌گردد.';
                btn.style.background = '#166534';
                btn.innerText = 'بله، فیش تایید شود';
                btn.onclick = () => submitPaletteC2CReview('approve');
            } else {
                iconWrap.style.background = '#fee2e2';
                iconWrap.style.color = '#991b1b';
                iconWrap.innerHTML = '✕';
                header.innerText = 'رد فیش واریزی سفارش';
                body.innerText = 'آیا از رد فیش و اعلام نامعتبر بودن تراکنش به کاربر اطمینان دارید؟';
                btn.style.background = '#991b1b';
                btn.innerText = 'رد فیش واریزی';
                btn.onclick = () => submitPaletteC2CReview('reject');
            }

            overlay.style.display = 'flex';
        }

        function closePaletteC2CModal() {
            const overlay = document.getElementById('palette-c2c-modal-overlay');
            if (overlay) overlay.style.display = 'none';
        }

        function submitPaletteC2CReview(action) {
            const btn = document.getElementById('c2c-modal-submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'در حال ثبت...';
            }

            const fd = new FormData();
            fd.append('action', 'serene_admin_verify_c2c_receipt');
            fd.append('order_id', currentC2COrderId);
            fd.append('verify_action', action);
            fd.append('nonce', '<?php echo wp_create_nonce('serene_panel_nonce'); ?>');

            fetch(ajaxurl, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => {
                closePaletteC2CModal();
                if (d.success) {
                    location.reload();
                } else {
                    alert(d.data?.message || 'خطا در پردازش درخواست.');
                    if (btn) btn.disabled = false;
                }
            })
            .catch(() => {
                closePaletteC2CModal();
                alert('خطا در ارتباط با سرور.');
                if (btn) btn.disabled = false;
            });
        }
        </script>
        <?php
    }

    public function render_thankyou_c2c_box($order_id) {
        static $rendered_orders = [];
        if (!empty($rendered_orders[$order_id])) {
            return;
        }
        $rendered_orders[$order_id] = true;

        $order = wc_get_order($order_id);
        if (!$order) return;

        global $wpdb;
        $table = $wpdb->prefix . 'serene_card_transfers';
        $existing = null;
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") === $table) {
            $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE order_id = %d ORDER BY id DESC LIMIT 1", $order_id));
        }

        $options = get_option('serene_panel_options', []);
        $card    = !empty($options['c2c_card_number']) ? $options['c2c_card_number'] : '6037991122334455';
        $holder  = !empty($options['c2c_card_holder']) ? $options['c2c_card_holder'] : 'فروشگاه پالت';
        $bank    = !empty($options['c2c_bank_name']) ? $options['c2c_bank_name'] : 'ملی';
        ?>
        <div id="palette-c2c-receipt-section" style="background:#ffffff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 28px; margin: 25px 0; font-family: inherit; direction: rtl; text-align: right; box-shadow: 0 10px 25px rgba(0,0,0,0.03);">
            <div style="display:flex; align-items:center; gap:10px; border-bottom:1px solid #f1f5f9; padding-bottom:16px; margin-bottom:20px;">
                <span class="material-symbols-outlined" style="font-size:28px; color:#4c5e8b;">credit_card</span>
                <h3 style="margin:0; font-size:16px; font-weight:800; color:#1e293b;">ثبت مشخصات و فیش واریز کارت به کارت</h3>
            </div>

            <!-- Card Information Banner -->
            <div style="background: linear-gradient(135deg, #1e293b, #334155); color:#fff; padding:20px; border-radius:18px; margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; font-size:12px; color:#cbd5e1; margin-bottom:10px;">
                    <span>بانک <?php echo esc_html($bank); ?></span>
                    <span>صاحب حساب: <?php echo esc_html($holder); ?></span>
                </div>
                <div style="font-family:monospace; font-size:20px; font-weight:bold; letter-spacing:3px; text-align:center; margin-bottom:10px; color:#f8fafc;" dir="ltr">
                    <?php echo esc_html($card); ?>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:12px; color:#94a3b8; border-top:1px solid rgba(255,255,255,0.1); padding-top:10px;">
                    <span>مبلغ قابل پرداخت:</span>
                    <span style="color:#38bdf8; font-weight:bold;"><?php echo number_format($order->get_total()); ?> تومان</span>
                </div>
            </div>

            <?php if ($existing): ?>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 18px; border-radius: 16px; font-size: 13px; font-weight: bold; display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined" style="font-size:24px;">check_circle</span>
                    <div>
                        <div>فیش واریزی شما با موفقیت ثبت شد و در حال حاضر در وضعیت «<?php echo esc_html($existing->status === 'approved' ? 'تایید شده' : 'در حال بررسی'); ?>» قرار دارد.</div>
                        <div style="font-size:11px; font-weight:normal; margin-top:4px; opacity:0.85;">کد پیگیری: <?php echo esc_html($existing->tracking_code ?: 'ثبت شده'); ?> | شماره فیش: #<?php echo $existing->id; ?></div>
                    </div>
                </div>
            <?php else: ?>
                <form id="palette-c2c-receipt-form" onsubmit="submitC2CReceipt(event, <?php echo $order_id; ?>)" style="display:flex; flex-direction:column; gap:16px;">
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:bold; color:#475569; margin-bottom:6px;">۴ رقم آخر کارت واریزکننده:</label>
                            <input type="text" id="c2c-sender-card" placeholder="مثال: 1234" maxlength="16" style="width:100%; border:1px solid #cbd5e1; border-radius:14px; padding:10px 14px; font-size:13px; font-family:monospace; text-align:left;" dir="ltr" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:bold; color:#475569; margin-bottom:6px;">کد رهگیری / شماره ارجاع بانکی:</label>
                            <input type="text" id="c2c-tracking-code" placeholder="مثال: 987654321" style="width:100%; border:1px solid #cbd5e1; border-radius:14px; padding:10px 14px; font-size:13px; font-family:monospace; text-align:left;" dir="ltr" required>
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:bold; color:#475569; margin-bottom:6px;">تصویر یا اسکرین‌شات فیش واریز (اختیاری):</label>
                        <input type="file" id="c2c-receipt-file" accept="image/*" style="width:100%; font-size:12px; color:#64748b;">
                    </div>

                    <button type="submit" id="c2c-submit-btn" style="background:#4c5e8b; color:#fff; border:none; border-radius:14px; padding:14px 24px; font-size:13px; font-weight:bold; cursor:pointer; transition:all 0.2s; align-self:flex-start;">
                        ثبت و ارسال فیش واریزی
                    </button>
                    <div id="c2c-form-msg" style="display:none; padding:12px; border-radius:12px; font-size:12px; font-weight:bold;"></div>
                </form>

                <script>
                function submitC2CReceipt(e, orderId) {
                    e.preventDefault();
                    const btn = document.getElementById('c2c-submit-btn');
                    const msg = document.getElementById('c2c-form-msg');
                    const senderCard = document.getElementById('c2c-sender-card').value.trim();
                    const trackingCode = document.getElementById('c2c-tracking-code').value.trim();
                    const fileInput = document.getElementById('c2c-receipt-file');

                    btn.disabled = true;
                    btn.innerText = 'در حال ارسال اطلاعات...';

                    const fd = new FormData();
                    fd.append('action', 'serene_submit_c2c_receipt');
                    fd.append('order_id', orderId);
                    fd.append('sender_card', senderCard);
                    fd.append('tracking_code', trackingCode);
                    if (fileInput.files.length > 0) {
                        fd.append('receipt_image', fileInput.files[0]);
                    }
                    fd.append('nonce', '<?php echo wp_create_nonce('serene_panel_nonce'); ?>');

                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: fd
                    })
                    .then(r => r.json())
                    .then(d => {
                        btn.disabled = false;
                        btn.innerText = 'ثبت و ارسال فیش واریزی';
                        msg.style.display = 'block';
                        if (d.success) {
                            msg.style.background = '#ecfdf5';
                            msg.style.color = '#065f46';
                            msg.innerText = d.data.message;
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            msg.style.background = '#fef2f2';
                            msg.style.color = '#991b1b';
                            msg.innerText = d.data.message || 'خطا در ثبت فیش';
                        }
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.innerText = 'ثبت و ارسال فیش واریزی';
                        msg.style.display = 'block';
                        msg.style.background = '#fef2f2';
                        msg.style.color = '#991b1b';
                        msg.innerText = 'خطای ارتباط با سرور';
                    });
                }
                </script>
            <?php endif; ?>
        </div>
        <?php
    }

    public function render_thankyou_c2c_fallback($order_id) {
        $order = wc_get_order($order_id);
        if ($order && in_array($order->get_payment_method(), ['palette_c2c', 'serene_c2c', 'c2c'], true)) {
            $this->render_thankyou_c2c_box($order_id);
        }
    }
}

// Backward Compatibility Class Alias
class Serene_Panel_Card_To_Card extends Palette_Panel_Card_To_Card {}
