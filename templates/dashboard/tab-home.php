<?php
if (!defined('ABSPATH')) exit;
?>
<div class="space-y-8">
    <!-- Welcome Header & Wallet Card (Matching rtl_1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
        <div class="lg:col-span-2 bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/30 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-primary bg-primary-fixed px-3 py-1 rounded-full">پیشخوان کاربری</span>
                <h1 class="text-2xl md:text-3xl font-black text-on-surface mt-3 mb-2">سلام، <?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?> عزیز! 👋</h1>
                <p class="text-sm text-on-surface-variant leading-relaxed">به پنل کاربری هوشمند خود خوش آمدید. از این بخش می‌توانید آخرین وضعیت سفارش‌ها، موجودی کیف پول و تیکت‌های پشتیبانی خود را بررسی و مدیریت کنید.</p>
            </div>
            <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t border-outline-variant/20">
                <a href="?tab=orders" class="bg-primary text-white hover:bg-primary-dim px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-md shadow-primary/20">
                    <span class="material-symbols-outlined text-sm">shopping_bag</span>
                    <span>مشاهده سفارشات</span>
                </a>
                <a href="?tab=lucky_wheel" class="bg-tertiary text-white hover:bg-tertiary-dim px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-md shadow-tertiary/20">
                    <span class="material-symbols-outlined text-sm">casino</span>
                    <span>گردونه شانس روزانه</span>
                </a>
                <a href="?tab=tickets" class="bg-surface-container-high text-on-surface hover:bg-surface-variant px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">chat</span>
                    <span>ثبت تیکت پشتیبانی</span>
                </a>
            </div>
        </div>

        <!-- Wallet Mini Card -->
        <div class="bg-gradient-to-br from-primary to-primary-dim text-white rounded-3xl p-6 shadow-xl flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <span class="text-xs text-white/80 font-medium">موجودی کیف پول</span>
                    <div class="text-2xl md:text-3xl font-black mt-2 tracking-tight"><?php echo number_format($wallet_balance); ?> <span class="text-xs font-normal text-white/70">تومان</span></div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
                </div>
            </div>
            <div class="relative z-10 pt-6 border-t border-white/10 flex justify-between items-center text-xs">
                <span class="text-white/80">کش‌بک فعال: <?php echo esc_html(get_option('serene_panel_options', [])['cashback_percent'] ?? 5); ?>٪</span>
                <a href="?tab=wallet" class="bg-white text-primary px-4 py-2 rounded-xl font-bold hover:bg-primary-container transition-all shadow-md">شارژ / مدیریت</a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <?php
        $orders_count = count(wc_get_orders(['customer_id' => $user_id, 'limit' => -1]));
        $tickets_count = count(Serene_Panel_Tickets::get_user_tickets($user_id, 100));
        ?>
        <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">shopping_cart</span>
            </div>
            <div>
                <div class="text-xl font-black text-on-surface"><?php echo $orders_count; ?></div>
                <div class="text-xs text-on-surface-variant">کل سفارش‌ها</div>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">forum</span>
            </div>
            <div>
                <div class="text-xl font-black text-on-surface"><?php echo $tickets_count; ?></div>
                <div class="text-xs text-on-surface-variant">تیکت‌های من</div>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">stars</span>
            </div>
            <div>
                <div class="text-xl font-black text-on-surface"><?php echo esc_html($tier['name']); ?></div>
                <div class="text-xs text-on-surface-variant">سطح باشگاه مشتریان</div>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">share</span>
            </div>
            <div>
                <div class="text-xl font-black text-on-surface"><?php echo esc_html(Serene_Panel_Affiliate::get_referral_code($user_id)); ?></div>
                <div class="text-xs text-on-surface-variant">کد معرف شما</div>
            </div>
        </div>
    </div>
</div>
