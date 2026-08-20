<?php
if (!defined('ABSPATH')) exit;
$billing_address = wc_get_account_formatted_address('billing');
$shipping_address = wc_get_account_formatted_address('shipping');
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-on-surface">آدرس‌های ارسال</h1>
        <p class="text-sm text-on-surface-variant mt-1">آدرس‌های پیش‌فرض صورت‌حساب و تحویل مرسولات پستی.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 shadow-sm space-y-4">
            <div class="flex items-center gap-2 text-primary font-bold text-sm">
                <span class="material-symbols-outlined">receipt_long</span>
                <span>آدرس صورت‌حساب</span>
            </div>
            <div class="text-xs leading-relaxed text-on-surface-variant bg-surface-container-low p-4 rounded-2xl min-h-[100px]">
                <?php echo $billing_address ?: 'آدرسی ثبت نشده است.'; ?>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 shadow-sm space-y-4">
            <div class="flex items-center gap-2 text-primary font-bold text-sm">
                <span class="material-symbols-outlined">local_shipping</span>
                <span>آدرس تحویل سفارش</span>
            </div>
            <div class="text-xs leading-relaxed text-on-surface-variant bg-surface-container-low p-4 rounded-2xl min-h-[100px]">
                <?php echo $shipping_address ?: 'آدرسی ثبت نشده است.'; ?>
            </div>
        </div>
    </div>
</div>
