<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Quick_Checkout {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function get_checkout_data() {
        if (!function_exists('WC') || !WC()->cart) {
            return null;
        }

        $cart = WC()->cart;
        $items = [];

        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
                $items[] = [
                    'key'       => $cart_item_key,
                    'name'      => $_product->get_name(),
                    'price'     => wc_price($_product->get_price()),
                    'quantity'  => $cart_item['quantity'],
                    'subtotal'  => wc_price($cart_item['line_subtotal']),
                    'image'     => wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail'),
                ];
            }
        }

        return [
            'items'     => $items,
            'total'     => $cart->get_total(),
            'subtotal'  => $cart->get_cart_subtotal(),
            'coupons'   => $cart->get_applied_coupons(),
            'gateways'  => WC()->payment_gateways->get_available_payment_gateways(),
        ];
    }
}
