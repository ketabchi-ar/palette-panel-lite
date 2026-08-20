<?php
if (!defined('ABSPATH')) exit;
$rewards = Serene_Panel_Lucky_Wheel::get_user_rewards($user_id);
$total_coupons = 0;
$active_coupons = 0;
foreach ($rewards as $r) {
    if ($r['type'] === 'coupon') {
        $total_coupons++;
        if ($r['status'] === 'active') $active_coupons++;
    }
}
?>
<div class="space-y-6 max-w-4xl mx-auto font-body">
    <!-- Header Banner -->
    <div class="bg-gradient-to-l from-tertiary to-primary rounded-3xl p-8 text-white shadow-xl flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="space-y-2 text-right">
            <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs font-bold backdrop-blur-sm">
                <span class="material-symbols-outlined text-sm">card_giftcard</span>
                <span>صندوق هدایا و کدهای تخفیف</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black">جوایز و پاداش‌های من 🎁</h1>
            <p class="text-white/80 text-xs md:text-sm leading-relaxed">کلیه کدهای تخفیف اختصاصی، جوایز گردونه شانس و پاداش‌های شما در این بخش نگهداری می‌شوند.</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-white/10 backdrop-blur-md px-5 py-3 rounded-2xl text-center border border-white/20">
                <div class="text-[11px] text-white/80">کوپن‌های فعال</div>
                <div class="text-xl font-black text-white mt-0.5"><?php echo $active_coupons; ?> <span class="text-xs font-normal">عدد</span></div>
            </div>
            <a href="?tab=lucky_wheel" class="bg-white text-primary hover:bg-primary-container px-4 py-3 rounded-2xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5 self-center">
                <span class="material-symbols-outlined text-base">casino</span>
                <span>چرخش گردونه</span>
            </a>
        </div>
    </div>

    <!-- Active Coupons Section -->
    <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl border border-outline-variant/30 shadow-sm space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">confirmation_number</span>
                <span>کدهای تخفیف و کوپن‌های اختصاصی شما</span>
            </h3>
            <span class="text-xs text-on-surface-variant font-bold"><?php echo count($rewards); ?> جایزه ثبت شده</span>
        </div>

        <?php if (empty($rewards)): ?>
            <div class="text-center py-12 space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-surface-container flex items-center justify-center mx-auto text-on-surface-variant">
                    <span class="material-symbols-outlined text-3xl">sentiment_dissatisfied</span>
                </div>
                <h4 class="text-sm font-bold text-on-surface">هنوز جایزه‌ای دریافت نکرده‌اید!</h4>
                <p class="text-xs text-on-surface-variant max-w-sm mx-auto">گردونه شانس روزانه را بچرخانید و جوایز و کدهای تخفیف شگفت‌انگیز برنده شوید.</p>
                <a href="?tab=lucky_wheel" class="inline-flex items-center gap-1.5 bg-primary text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md">
                    <span>امتحان شانس در گردونه</span>
                    <span class="material-symbols-outlined text-sm">casino</span>
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($rewards as $reward): 
                    $is_coupon = ($reward['type'] === 'coupon');
                ?>
                    <div class="bg-surface-container-low p-5 rounded-2xl border border-outline-variant/30 space-y-3 flex flex-col justify-between hover:shadow-md transition-all">
                        <div class="flex justify-between items-start gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl <?php echo $is_coupon ? 'bg-primary/10 text-primary' : 'bg-purple-100 text-purple-700'; ?> flex items-center justify-center">
                                    <span class="material-symbols-outlined text-xl"><?php echo $is_coupon ? 'local_activity' : 'account_balance_wallet'; ?></span>
                                </div>
                                <div>
                                    <div class="font-black text-sm text-on-surface"><?php echo esc_html($reward['title']); ?></div>
                                    <div class="text-[10px] text-on-surface-variant mt-0.5">دریافت شده در: <?php echo esc_html(date_i18n('Y/m/d - H:i', strtotime($reward['created_at']))); ?></div>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border <?php echo esc_attr($reward['status_class']); ?>">
                                <?php echo esc_html($reward['status_label']); ?>
                            </span>
                        </div>

                        <?php if ($is_coupon && !empty($reward['code'])): ?>
                            <div class="bg-white p-3 rounded-xl border border-dashed border-outline-variant flex items-center justify-between gap-2" dir="ltr">
                                <span class="font-mono font-black text-sm text-primary select-all"><?php echo esc_html($reward['code']); ?></span>
                                <button type="button" onclick="navigator.clipboard.writeText('<?php echo esc_js($reward['code']); ?>'); alert('کد کوپن با موفقیت کپی شد!');" class="bg-surface-container hover:bg-surface-container-high text-on-surface px-3 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                    <span class="material-symbols-outlined text-sm">content_copy</span>
                                    <span>کپی</span>
                                </button>
                            </div>

                            <div class="flex justify-between items-center pt-1 text-[11px] text-on-surface-variant">
                                <div>
                                    <?php if ($reward['expires_at']): ?>
                                        <span>انقضا: </span>
                                        <strong class="font-mono"><?php echo esc_html(date_i18n('Y/m/d', strtotime($reward['expires_at']))); ?></strong>
                                    <?php endif; ?>
                                </div>
                                <?php if ($reward['status'] === 'active'): ?>
                                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="text-primary font-bold hover:underline inline-flex items-center gap-1">
                                        <span>خرید با این کد</span>
                                        <span class="material-symbols-outlined text-xs">arrow_left</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
