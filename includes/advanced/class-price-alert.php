<?php
if (!defined('ABSPATH')) {
    exit;
}

class Palette_Panel_Price_Alert {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('woocommerce_single_product_summary', [$this, 'render_product_alert_buttons'], 32);
        add_action('woocommerce_product_set_stock_status', [$this, 'handle_stock_change'], 10, 3);
        add_action('woocommerce_update_product', [$this, 'handle_product_update'], 10, 2);
        add_shortcode('palette_price_alert', [$this, 'render_shortcode']);
        add_shortcode('serene_price_alert', [$this, 'render_shortcode']);
    }

    public function render_product_alert_buttons() {
        global $product;
        if (!$product) return;

        $opt = get_option('serene_panel_options', []);
        if (empty($opt['enable_price_alerts'])) return;

        $product_id = $product->get_id();
        $is_in_stock = $product->is_in_stock();
        ?>
        <div class="palette-product-alerts-wrapper" style="margin: 15px 0; display: flex; flex-wrap: wrap; gap: 8px; font-family: inherit;">
            <?php if (!$is_in_stock): ?>
                <button type="button" onclick="openPriceAlertModal(<?php echo $product_id; ?>, 'stock_back', 'موجود شد خبرم کن 🔔')" class="palette-alert-btn" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #1e293b; padding: 10px 16px; border-radius: 14px; font-size: 12px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
                    <span>🔔</span>
                    <span>موجود شد به من اطلاع بده</span>
                </button>
            <?php else: ?>
                <button type="button" onclick="openPriceAlertModal(<?php echo $product_id; ?>, 'price_drop', 'ارزان شد خبرم کن 📉')" class="palette-alert-btn" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #1e293b; padding: 10px 16px; border-radius: 14px; font-size: 12px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
                    <span>📉</span>
                    <span>ارزان شد به من اطلاع بده</span>
                </button>
            <?php endif; ?>
        </div>
        <?php
        $this->render_alert_modal();
    }

    public function render_shortcode($atts) {
        $a = shortcode_atts(['product_id' => 0], $atts);
        $pid = intval($a['product_id']) ?: get_the_ID();
        if (!$pid) return '';

        ob_start();
        ?>
        <button type="button" onclick="openPriceAlertModal(<?php echo $pid; ?>, 'price_drop', 'اطلاع‌رسانی تغییر قیمت 🔔')" style="background: #4c5e8b; color: #fff; padding: 10px 16px; border-radius: 12px; font-size: 12px; font-weight: bold; border:none; cursor: pointer;">
            🔔 اطلاع‌رسانی کاهش قیمت این محصول
        </button>
        <?php
        $this->render_alert_modal();
        return ob_get_clean();
    }

    private function render_alert_modal() {
        static $rendered = false;
        if ($rendered) return;
        $rendered = true;

        $current_user = wp_get_current_user();
        $user_phone = $current_user->ID ? (get_user_meta($current_user->ID, '_serene_phone', true) ?: get_user_meta($current_user->ID, 'billing_phone', true)) : '';
        ?>
        <div id="palette-price-alert-modal" style="display:none; position: fixed; inset:0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 99999; align-items:center; justify-content:center; padding: 15px; font-family: inherit;" dir="rtl">
            <div style="background:#fff; border-radius:24px; max-width:400px; width:100%; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.2); border:1px solid #e2e8f0; text-align:right;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h4 id="palette-alert-modal-title" style="margin:0; font-size:15px; font-weight:900; color:#1e293b;">اطلاع‌رسانی محصول</h4>
                    <button type="button" onclick="closePriceAlertModal()" style="background:none; border:none; font-size:20px; color:#64748b; cursor:pointer;">✕</button>
                </div>
                <p style="font-size:12px; color:#64748b; margin-bottom:15px; line-height:1.6;">
                    شماره همراه خود را وارد نمایید تا به محض موجود شدن یا کاهش قیمت محصول، از طریق پیامک به شما اطلاع دهیم.
                </p>

                <input type="hidden" id="palette-alert-pid" value="">
                <input type="hidden" id="palette-alert-type" value="price_drop">

                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:6px; color:#334155;">شماره موبایل:</label>
                    <input type="tel" id="palette-alert-phone" value="<?php echo esc_attr($user_phone); ?>" placeholder="09123456789" dir="ltr" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:14px; font-size:13px; font-family:monospace; text-align:center;">
                </div>

                <button type="button" onclick="submitPriceAlert()" id="btn-submit-price-alert" style="width:100%; background:#4c5e8b; color:#fff; font-weight:bold; padding:14px; border:none; border-radius:14px; cursor:pointer; font-size:13px; shadow:0 4px 12px rgba(76,94,139,0.3);">
                    ثبت و فعالسازی اطلاع‌رسانی
                </button>

                <div id="palette-alert-msg" style="display:none; margin-top:12px; padding:10px; border-radius:12px; font-size:12px;"></div>
            </div>
        </div>

        <script>
        function openPriceAlertModal(pid, type, title) {
            document.getElementById('palette-alert-pid').value = pid;
            document.getElementById('palette-alert-type').value = type;
            document.getElementById('palette-alert-modal-title').innerText = title;
            document.getElementById('palette-price-alert-modal').style.display = 'flex';
        }
        function closePriceAlertModal() {
            document.getElementById('palette-price-alert-modal').style.display = 'none';
        }
        function submitPriceAlert() {
            const pid = document.getElementById('palette-alert-pid').value;
            const type = document.getElementById('palette-alert-type').value;
            const phone = document.getElementById('palette-alert-phone').value;
            const btn = document.getElementById('btn-submit-price-alert');
            const msg = document.getElementById('palette-alert-msg');

            if (!phone) {
                alert('لطفاً شماره موبایل را وارد نمایید.');
                return;
            }

            btn.disabled = true;
            btn.innerText = 'در حال ثبت...';

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'serene_subscribe_price_alert',
                    product_id: pid,
                    alert_type: type,
                    phone: phone,
                    nonce: '<?php echo wp_create_nonce('serene_panel_nonce'); ?>'
                })
            })
            .then(r => r.json())
            .then(d => {
                btn.disabled = false;
                btn.innerText = 'ثبت و فعالسازی اطلاع‌رسانی';
                msg.style.display = 'block';
                if (d.success) {
                    msg.style.background = '#ecfdf5';
                    msg.style.color = '#065f46';
                    msg.innerText = d.data.message;
                    setTimeout(closePriceAlertModal, 2000);
                } else {
                    msg.style.background = '#fef2f2';
                    msg.style.color = '#991b1b';
                    msg.innerText = d.data.message || 'خطا در ثبت';
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = 'ثبت و فعالسازی اطلاع‌رسانی';
                msg.style.display = 'block';
                msg.style.background = '#fef2f2';
                msg.style.color = '#991b1b';
                msg.innerText = 'خطای ارتباط با سرور';
            });
        }
        </script>
        <?php
    }

    public static function add_alert($user_id, $product_id, $type = 'price_drop', $phone = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_price_alerts';

        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE (user_id = %d OR phone = %s) AND product_id = %d AND alert_type = %s AND is_notified = 0",
            $user_id, $phone, $product_id, $type
        ));

        if ($exists) {
            return true;
        }

        $product = wc_get_product($product_id);
        $current_price = $product ? floatval($product->get_price()) : 0;

        return $wpdb->insert($table, [
            'user_id'      => (int) $user_id,
            'phone'        => sanitize_text_field($phone),
            'product_id'   => (int) $product_id,
            'target_price' => $current_price,
            'alert_type'   => sanitize_text_field($type),
            'is_notified'  => 0,
            'created_at'   => current_time('mysql'),
        ]);
    }

    public function handle_stock_change($product_id, $stock_status, $product) {
        if ($stock_status === 'instock') {
            self::notify_subscribers($product_id, 'stock_back');
        }
    }

    public function handle_product_update($product_id, $product) {
        if (!$product) return;
        if ($product->is_in_stock()) {
            self::notify_subscribers($product_id, 'stock_back');
        }
    }

    public static function notify_subscribers($product_id, $type) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_price_alerts';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") !== $table) return;

        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE product_id = %d AND alert_type = %s AND is_notified = 0",
            $product_id, $type
        ));

        if (empty($rows)) return;

        $product = wc_get_product($product_id);
        $product_title = $product ? $product->get_name() : 'محصول انتخابی شما';

        foreach ($rows as $row) {
            $phone = $row->phone;
            if (empty($phone) && $row->user_id) {
                $phone = get_user_meta($row->user_id, '_serene_phone', true) ?: get_user_meta($row->user_id, 'billing_phone', true);
            }

            if ($phone) {
                $msg = ($type === 'stock_back') 
                    ? sprintf("سلام! محصول «%s» در فروشگاه مجدداً موجود شد: %s", $product_title, get_permalink($product_id))
                    : sprintf("سلام! قیمت محصول «%s» کاهش یافت: %s", $product_title, get_permalink($product_id));

                if (class_exists('Serene_Panel_OTP_Service')) {
                    $gateway = Serene_Panel_OTP_Service::get_gateway();
                    if ($gateway) {
                        $gateway->send_custom_sms($phone, $msg);
                    }
                }
            }

            $wpdb->update($table, ['is_notified' => 1], ['id' => $row->id]);
        }
    }
}

// Backward Compatibility Class Alias
class Serene_Panel_Price_Alert extends Palette_Panel_Price_Alert {}
