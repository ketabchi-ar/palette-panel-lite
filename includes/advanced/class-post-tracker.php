<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Post_Tracker {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('add_meta_boxes', [$this, 'register_tracking_meta_box']);
        add_action('save_post_shop_order', [$this, 'save_tracking_meta_box'], 10, 2);
        add_action('woocommerce_process_shop_order_meta', [$this, 'save_tracking_meta_box_hpos'], 10, 2);
    }

    public static function get_default_carriers() {
        return [
            'post_pishtaz' => [
                'name' => 'پست پیشتاز جمهوری اسلامی ایران',
                'url'  => 'https://tracking.post.ir/?id={code}',
                'icon' => 'local_post_office',
            ],
            'tipax' => [
                'name' => 'شرکت تیپاکس (Tipax)',
                'url'  => 'https://tipaxco.com/tracking?id={code}',
                'icon' => 'local_shipping',
            ],
            'chapar' => [
                'name' => 'شرکت چاپار (Chapar)',
                'url'  => 'https://tracking.chaparnet.com/?tracking_number={code}',
                'icon' => 'local_shipping',
            ],
            'mahex' => [
                'name' => 'شرکت ماهکس (Mahex)',
                'url'  => 'https://mahex.com/tracking?id={code}',
                'icon' => 'local_shipping',
            ],
            'snappbox' => [
                'name' => 'اسنپ‌باکس / تپسی‌پک (پیک آنلاین)',
                'url'  => '',
                'icon' => 'two_wheeler',
            ],
            'courier' => [
                'name' => 'پیک اختصاصی فروشگاه',
                'url'  => '',
                'icon' => 'directions_bike',
            ],
        ];
    }

    public static function get_carriers() {
        $saved = get_option('serene_shipping_carriers', null);
        if ($saved && is_array($saved) && !empty($saved)) {
            return $saved;
        }
        return self::get_default_carriers();
    }

    public static function save_carriers($carriers) {
        if (!is_array($carriers)) return false;
        $clean = [];
        foreach ($carriers as $key => $data) {
            $slug = sanitize_key($key);
            if (!empty($slug) && !empty($data['name'])) {
                $clean[$slug] = [
                    'name' => sanitize_text_field($data['name']),
                    'url'  => esc_url_raw($data['url'] ?? ''),
                    'icon' => sanitize_text_field($data['icon'] ?? 'local_shipping'),
                ];
            }
        }
        return update_option('serene_shipping_carriers', $clean);
    }

    public static function get_tracking_url($carrier_slug, $tracking_code) {
        $carriers = self::get_carriers();
        $code = trim(sanitize_text_field($tracking_code));
        if (empty($code)) return '';

        if (isset($carriers[$carrier_slug]) && !empty($carriers[$carrier_slug]['url'])) {
            $url_template = $carriers[$carrier_slug]['url'];
            return str_replace(['{code}', '{tracking_code}', '[code]'], urlencode($code), $url_template);
        }

        // Default to Post.ir if not specified
        return 'https://tracking.post.ir/?id=' . urlencode($code);
    }

    public function register_tracking_meta_box() {
        $screens = ['shop_order', 'woocommerce_page_wc-orders'];
        foreach ($screens as $screen) {
            add_meta_box(
                'serene_post_tracking_box',
                '📦 رهگیری مرسوله پستی (پالت پنل)',
                [$this, 'render_tracking_meta_box'],
                $screen,
                'side',
                'default'
            );
        }
    }

    public function render_tracking_meta_box($post_or_order) {
        $order_id = is_object($post_or_order) && method_exists($post_or_order, 'get_id') ? $post_or_order->get_id() : (is_object($post_or_order) ? $post_or_order->ID : $post_or_order);
        $tracking_code = self::get_order_tracking_code($order_id);
        $selected_carrier = get_post_meta($order_id, '_serene_carrier_slug', true) ?: 'post_pishtaz';
        $carriers = self::get_carriers();
        $tracking_url = self::get_tracking_url($selected_carrier, $tracking_code);

        wp_nonce_field('serene_save_tracking_nonce', 'serene_tracking_nonce');
        ?>
        <div style="font-family: inherit; font-size: 12px; line-height: 1.6;" dir="rtl">
            <p style="margin-bottom: 6px;">
                <label for="serene_carrier_slug" style="font-weight: bold; display: block; margin-bottom: 3px;">شرکت حمل‌ونقل / سرویس ارسال:</label>
                <select id="serene_carrier_slug" name="serene_carrier_slug" style="width: 100%; font-size: 12px;">
                    <?php foreach ($carriers as $slug => $c): ?>
                        <option value="<?php echo esc_attr($slug); ?>" <?php selected($selected_carrier, $slug); ?>>
                            <?php echo esc_html($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p style="margin-bottom: 6px;">
                <label for="serene_post_tracking_code" style="font-weight: bold; display: block; margin-bottom: 3px;">کد / بارکد رهگیری مرسوله:</label>
                <input type="text" id="serene_post_tracking_code" name="serene_post_tracking_code" value="<?php echo esc_attr($tracking_code); ?>" placeholder="مثال: 123456789012345678901234" style="width: 100%; font-family: monospace; text-align: left; font-size: 13px;" dir="ltr">
            </p>

            <?php if ($tracking_code && $tracking_url): ?>
                <p style="margin-top: 10px;">
                    <a href="<?php echo esc_url($tracking_url); ?>" target="_blank" class="button button-secondary" style="width: 100%; text-align: center; font-size: 12px;">
                        🔍 مشاهده آنلاین در سامانه شرکت
                    </a>
                </p>
            <?php endif; ?>
            <p style="color: #64748b; font-size: 11px; margin-top: 8px;">
                با درج کد، دکمه «رهگیری مرسوله» در پیشخوان مشتری فعال و لینک اختصاصی همان شرکت باز خواهد شد.
            </p>
        </div>
        <?php
    }

    public function save_tracking_meta_box($post_id, $post = null) {
        if (!isset($_POST['serene_tracking_nonce']) || !wp_verify_nonce($_POST['serene_tracking_nonce'], 'serene_save_tracking_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_shop_orders', $post_id)) return;

        if (isset($_POST['serene_post_tracking_code'])) {
            $code = sanitize_text_field($_POST['serene_post_tracking_code']);
            update_post_meta($post_id, '_serene_post_tracking_code', $code);
        }

        if (isset($_POST['serene_carrier_slug'])) {
            $slug = sanitize_key($_POST['serene_carrier_slug']);
            update_post_meta($post_id, '_serene_carrier_slug', $slug);
            $carriers = self::get_carriers();
            if (isset($carriers[$slug])) {
                update_post_meta($post_id, '_serene_carrier_name', $carriers[$slug]['name']);
            }
        }
    }

    public function save_tracking_meta_box_hpos($order_id, $order = null) {
        $this->save_tracking_meta_box($order_id);
    }

    public static function get_order_tracking_code($order_id) {
        if (!$order_id) return '';

        $code = get_post_meta($order_id, '_serene_post_tracking_code', true);
        if ($code) return sanitize_text_field($code);

        $fallbacks = [
            '_tracking_number',
            'tracking_code',
            '_tracking_code',
            'post_tracking_code',
            '_wc_shipment_tracking_items',
            '_persian_woocommerce_sms_tracking_code',
            'barcode'
        ];

        foreach ($fallbacks as $meta_key) {
            $val = get_post_meta($order_id, $meta_key, true);
            if (!empty($val)) {
                if (is_array($val)) {
                    if (isset($val[0]['tracking_number'])) return sanitize_text_field($val[0]['tracking_number']);
                    if (isset($val['tracking_number'])) return sanitize_text_field($val['tracking_number']);
                } else {
                    return sanitize_text_field($val);
                }
            }
        }

        return '';
    }

    public static function get_order_tracking_info($order_id) {
        $code = self::get_order_tracking_code($order_id);
        $slug = get_post_meta($order_id, '_serene_carrier_slug', true) ?: 'post_pishtaz';
        $carriers = self::get_carriers();
        $carrier = $carriers[$slug] ?? ['name' => 'شرکت ملی پست ایران', 'url' => 'https://tracking.post.ir/?id={code}', 'icon' => 'local_shipping'];

        return [
            'tracking_code' => $code,
            'carrier_slug'  => $slug,
            'carrier_name'  => $carrier['name'],
            'carrier_icon'  => $carrier['icon'] ?? 'local_shipping',
            'tracking_url'  => self::get_tracking_url($slug, $code),
        ];
    }
}
