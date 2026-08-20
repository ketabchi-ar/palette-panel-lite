<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_PDF_Invoice {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'handle_invoice_download']);
        add_action('woocommerce_admin_order_actions_end', [$this, 'add_admin_order_invoice_action']);
    }

    public function handle_invoice_download() {
        if (isset($_GET['serene_invoice'])) {
            $order_id = intval($_GET['serene_invoice']);
            $order    = wc_get_order($order_id);

            if (!$order) {
                wp_die('سفارش یافت نشد.');
            }

            if (!current_user_can('manage_woocommerce') && !current_user_can('edit_shop_orders')) {
                if (!is_user_logged_in() || $order->get_customer_id() !== get_current_user_id()) {
                    wp_die('دسترسی به این فاکتور برای شما مجاز نیست.');
                }
            }

            self::render_invoice_html($order);
            exit;
        }
    }

    public function add_admin_order_invoice_action($order) {
        if (!$order) return;
        $url = add_query_arg(['serene_invoice' => $order->get_id()], home_url('/'));
        printf(
            '<a class="button wc-action-button wc-action-button-invoice" href="%s" target="_blank" title="چاپ فاکتور پالت پنل" style="display:inline-flex;align-items:center;justify-content:center;">📄</a>',
            esc_url($url)
        );
    }

    public static function render_invoice_html($order) {
        $options = get_option('serene_panel_options', []);
        
        $seller_name    = $options['invoice_seller_name'] ?? get_bloginfo('name');
        $seller_logo    = $options['invoice_logo_url'] ?? '';
        $seller_econ    = $options['invoice_economic_code'] ?? '';
        $seller_national= $options['invoice_national_id'] ?? '';
        $seller_reg     = $options['invoice_registration_no'] ?? '';
        $seller_phone   = $options['invoice_phone'] ?? '';
        $seller_address = $options['invoice_address'] ?? '';
        $seller_postcode= $options['invoice_postal_code'] ?? '';
        $invoice_color  = $options['invoice_color'] ?? '#4c5e8b';
        $footer_note    = $options['invoice_footer_note'] ?? 'از خرید و اعتماد شما سپاسگزاریم.';

        $order_id   = $order->get_id();
        $order_num  = $order->get_order_number();
        $date       = date_i18n('Y/m/d - H:i', strtotime($order->get_date_created()));
        $pay_method = $order->get_payment_method_title();
        $status     = wc_get_order_status_name($order->get_status());

        $buyer_name    = $order->get_formatted_billing_full_name() ?: $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $buyer_phone   = $order->get_billing_phone();
        $buyer_email   = $order->get_billing_email();
        $buyer_address = $order->get_formatted_billing_address() ?: ($order->get_billing_state() . '، ' . $order->get_billing_city() . '، ' . $order->get_billing_address_1());
        $buyer_postcode= $order->get_billing_postcode();
        $buyer_national= get_post_meta($order_id, '_billing_national_code', true) ?: get_user_meta($order->get_customer_id(), 'billing_national_code', true);

        ?>
        <!DOCTYPE html>
        <html dir="rtl" lang="fa">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>فاکتور رسمی سفارش #<?php echo esc_html($order_num); ?></title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;700;900&display=swap">
            <style>
                * { box-sizing: border-box; }
                body {
                    font-family: 'Vazirmatn', Tahoma, sans-serif;
                    background: #f1f5f9;
                    color: #1e293b;
                    margin: 0;
                    padding: 20px;
                    font-size: 12px;
                    line-height: 1.6;
                }
                .invoice-wrapper {
                    max-width: 900px;
                    margin: auto;
                    background: #ffffff;
                    padding: 35px 40px;
                    border-radius: 20px;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
                    border: 1px solid #e2e8f0;
                }
                .action-bar {
                    max-width: 900px;
                    margin: 0 auto 15px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .btn {
                    background: <?php echo esc_attr($invoice_color); ?>;
                    color: #fff;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 12px;
                    font-family: inherit;
                    font-size: 12px;
                    font-weight: bold;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    text-decoration: none;
                }
                .btn:hover { opacity: 0.9; }
                .header-table {
                    width: 100%;
                    border-bottom: 2px solid <?php echo esc_attr($invoice_color); ?>;
                    padding-bottom: 20px;
                    margin-bottom: 20px;
                }
                .section-title {
                    font-size: 13px;
                    font-weight: 900;
                    color: <?php echo esc_attr($invoice_color); ?>;
                    background: #f8fafc;
                    padding: 8px 12px;
                    border-radius: 8px;
                    margin-bottom: 10px;
                    border-right: 4px solid <?php echo esc_attr($invoice_color); ?>;
                }
                .info-grid {
                    width: 100%;
                    margin-bottom: 20px;
                    border-collapse: collapse;
                }
                .info-grid td {
                    padding: 6px 10px;
                    vertical-align: top;
                }
                .info-box {
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 15px;
                    margin-bottom: 20px;
                }
                .items-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                .items-table th {
                    background: <?php echo esc_attr($invoice_color); ?>;
                    color: #fff;
                    padding: 10px;
                    font-weight: bold;
                    font-size: 12px;
                    text-align: right;
                }
                .items-table td {
                    padding: 10px;
                    border-bottom: 1px solid #e2e8f0;
                    text-align: right;
                }
                .items-table tr:nth-child(even) {
                    background: #f8fafc;
                }
                .summary-table {
                    width: 350px;
                    margin-right: auto;
                    border-collapse: collapse;
                }
                .summary-table td {
                    padding: 6px 10px;
                }
                .total-row {
                    background: #f1f5f9;
                    font-weight: 900;
                    font-size: 14px;
                    color: <?php echo esc_attr($invoice_color); ?>;
                }
                .footer-box {
                    border-top: 1px dashed #cbd5e1;
                    padding-top: 20px;
                    margin-top: 30px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-size: 11px;
                    color: #64748b;
                }
                @media print {
                    body { background: #fff; padding: 0; }
                    .invoice-wrapper { box-shadow: none; border: none; padding: 0; }
                    .action-bar { display: none; }
                    @page { size: A4; margin: 10mm; }
                }
            </style>
        </head>
        <body>
            <div class="action-bar">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn" style="background:#64748b;">بازگشت به سایت</a>
                <button class="btn" onclick="window.print()">🖨️ چاپ یا ذخیره فاکتور رسمی</button>
            </div>

            <div class="invoice-wrapper">
                <!-- Header -->
                <table class="header-table">
                    <tr>
                        <td style="width: 50%;">
                            <?php if ($seller_logo): ?>
                                <img src="<?php echo esc_url($seller_logo); ?>" style="max-height: 55px; margin-bottom: 8px; object-fit: contain;">
                            <?php endif; ?>
                            <h1 style="margin: 0; font-size: 18px; font-weight: 900; color: <?php echo esc_attr($invoice_color); ?>;"><?php echo esc_html($seller_name); ?></h1>
                            <p style="margin: 3px 0 0; color: #64748b; font-size: 11px;">صورت‌حساب فروش کالا و خدمات</p>
                        </td>
                        <td style="width: 50%; text-align: left;" dir="ltr">
                            <div style="font-size: 13px; font-weight: 900; color: #0f172a; margin-bottom: 4px;">شماره سفارش: #<?php echo esc_html($order_num); ?></div>
                            <div style="font-size: 11px; color: #64748b;">تاریخ ثبت: <?php echo esc_html($date); ?></div>
                            <div style="font-size: 11px; color: #64748b;">روش پرداخت: <?php echo esc_html($pay_method); ?></div>
                            <div style="font-size: 11px; color: #059669; font-weight: bold;">وضعیت: <?php echo esc_html($status); ?></div>
                        </td>
                    </tr>
                </table>

                <!-- Seller Info -->
                <div class="info-box">
                    <div class="section-title">مشخصات فروشنده</div>
                    <table class="info-grid">
                        <tr>
                            <td style="width: 33%;"><strong>فروشنده:</strong> <?php echo esc_html($seller_name); ?></td>
                            <td style="width: 33%;"><strong>شماره اقتصادی:</strong> <?php echo esc_html($seller_econ ?: '-'); ?></td>
                            <td style="width: 33%;"><strong>شناسه ملی / ثبت:</strong> <?php echo esc_html($seller_national ?: ($seller_reg ?: '-')); ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>نشانی:</strong> <?php echo esc_html($seller_address ?: 'دفتر مرکزی'); ?></td>
                            <td><strong>کد پستی:</strong> <?php echo esc_html($seller_postcode ?: '-'); ?> | <strong>تلفن:</strong> <?php echo esc_html($seller_phone ?: '-'); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Buyer Info -->
                <div class="info-box">
                    <div class="section-title">مشخصات خریدار</div>
                    <table class="info-grid">
                        <tr>
                            <td style="width: 33%;"><strong>خریدار:</strong> <?php echo esc_html($buyer_name); ?></td>
                            <td style="width: 33%;"><strong>شماره همراه:</strong> <?php echo esc_html($buyer_phone ?: '-'); ?></td>
                            <td style="width: 33%;"><strong>کد ملی / اقتصادی:</strong> <?php echo esc_html($buyer_national ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>نشانی تحویل:</strong> <?php echo esc_html($buyer_address ?: '-'); ?></td>
                            <td><strong>کد پستی:</strong> <?php echo esc_html($buyer_postcode ?: '-'); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Items Table -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 6%; text-align: center;">ردیف</th>
                            <th style="width: 44%;">شرح کالا یا خدمات</th>
                            <th style="width: 10%; text-align: center;">تعداد</th>
                            <th style="width: 20%;">مبلغ واحد (تومان)</th>
                            <th style="width: 20%;">مجموع کل (تومان)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $row_num = 1; foreach ($order->get_items() as $item): ?>
                        <tr>
                            <td style="text-align: center; font-weight: bold;"><?php echo $row_num++; ?></td>
                            <td>
                                <strong><?php echo esc_html($item->get_name()); ?></strong>
                                <?php 
                                $sku = $item->get_product() ? $item->get_product()->get_sku() : '';
                                if ($sku) echo '<br><small style="color:#64748b;">کد کالا: ' . esc_html($sku) . '</small>';
                                ?>
                            </td>
                            <td style="text-align: center; font-weight: bold;"><?php echo esc_html($item->get_quantity()); ?></td>
                            <td><?php echo number_format($order->get_item_subtotal($item, false, true)); ?></td>
                            <td><?php echo number_format($item->get_total()); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Summary Table -->
                <table class="summary-table" style="margin-right: 0; margin-left: auto;">
                    <tr>
                        <td>جمع اقلام:</td>
                        <td style="text-align: left; font-weight: bold;"><?php echo number_format($order->get_subtotal()); ?> تومان</td>
                    </tr>
                    <?php if ($order->get_discount_total() > 0): ?>
                    <tr>
                        <td style="color: #dc2626;">تخفیف:</td>
                        <td style="text-align: left; color: #dc2626; font-weight: bold;">-<?php echo number_format($order->get_discount_total()); ?> تومان</td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($order->get_shipping_total() > 0): ?>
                    <tr>
                        <td>هزینه ارسال (<?php echo esc_html($order->get_shipping_method()); ?>):</td>
                        <td style="text-align: left; font-weight: bold;"><?php echo number_format($order->get_shipping_total()); ?> تومان</td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($order->get_total_tax() > 0): ?>
                    <tr>
                        <td>مالیات بر ارزش افزوده:</td>
                        <td style="text-align: left; font-weight: bold;"><?php echo number_format($order->get_total_tax()); ?> تومان</td>
                    </tr>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td style="border-radius: 8px 0 0 8px;">مبلغ نهایی قابل پرداخت:</td>
                        <td style="text-align: left; border-radius: 0 8px 8px 0;"><?php echo number_format($order->get_total()); ?> تومان</td>
                    </tr>
                </table>

                <!-- Footer & Seal -->
                <div class="footer-box">
                    <div>
                        <p style="margin: 0; font-weight: bold; color: #334155;"><?php echo esc_html($footer_note); ?></p>
                        <p style="margin: 4px 0 0;">این سند به صورت سیستمی تولید شده و دارای اعتبار قانونی است.</p>
                    </div>
                    <div style="text-align: left;">
                        <p style="margin: 0; font-weight: bold;">مهر و امضای فروشنده</p>
                        <div style="height: 45px;"></div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}
