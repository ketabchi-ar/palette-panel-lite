<?php
if (!defined('ABSPATH')) exit;
$stats = Serene_Panel_Affiliate::get_user_stats($user_id);
?>
<div class="space-y-6 max-w-4xl mx-auto font-body">
    <!-- Header Banner -->
    <div class="bg-gradient-to-l from-primary to-primary-dim rounded-3xl p-8 text-white shadow-xl flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="space-y-2 text-right">
            <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs font-bold backdrop-blur-sm">
                <span class="material-symbols-outlined text-sm">handshake</span>
                <span>سیستم درآمدزایی و همکاری در فروش</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black">کسب درآمد با معرفی به دوستان</h1>
            <p class="text-white/80 text-xs md:text-sm">لینک اختصاصی خود را به اشتراک بگذارید و به ازای هر خرید، <?php echo esc_html($stats['rate']); ?>٪ پورسانت مستقیم در کیف پول خود دریافت کنید!</p>
        </div>
        <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl text-center border border-white/20">
            <div class="text-xs text-white/80">مجموع درآمد شما:</div>
            <div class="text-2xl font-black text-white mt-1"><?php echo number_format($stats['total_earnings']); ?> <span class="text-xs font-normal">تومان</span></div>
        </div>
    </div>

    <!-- Referral Link Card -->
    <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl border border-outline-variant/30 shadow-sm space-y-6">
        <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">link</span>
            <span>لینک اختصاصی بازاریابی شما</span>
        </h3>

        <div class="flex flex-col md:flex-row items-center gap-3">
            <div class="flex-1 w-full bg-surface-container-low p-3.5 rounded-2xl font-mono text-xs text-on-surface flex items-center justify-between border border-outline-variant/30 overflow-hidden" dir="ltr">
                <span id="affiliate-link-text" class="truncate"><?php echo esc_url($stats['link']); ?></span>
            </div>
            <button type="button" onclick="copyAffiliateLink()" id="btn-copy-aff" class="w-full md:w-auto bg-primary hover:bg-primary-dim text-white px-6 py-3.5 rounded-2xl text-xs font-bold shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-lg">content_copy</span>
                <span>کپی لینک معرف</span>
            </button>
        </div>

        <div class="flex items-center gap-3 pt-2 text-xs text-on-surface-variant">
            <span class="font-bold">کد معرف شما:</span>
            <span class="font-mono font-bold bg-surface-container-high px-3 py-1 rounded-xl text-primary select-all"><?php echo esc_html($stats['code']); ?></span>
        </div>
    </div>

    <!-- Step Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-black text-lg">
                ۱
            </div>
            <h4 class="font-bold text-sm text-on-surface">اشتراک‌گذاری لینک</h4>
            <p class="text-xs text-on-surface-variant leading-relaxed">لینک یا کد معرف خود را برای دوستان، کانال‌ها یا شبکه‌های اجتماعی بفرستید.</p>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-black text-lg">
                ۲
            </div>
            <h4 class="font-bold text-sm text-on-surface">ثبت سفارش توسط دوست</h4>
            <p class="text-xs text-on-surface-variant leading-relaxed">هر کاربری که با لینک شما وارد شده و تا ۳۰ روز خرید کند، به عنوان معرف او ثبت می‌شوید.</p>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-black text-lg">
                ۳
            </div>
            <h4 class="font-bold text-sm text-on-surface">واریز آنی به کیف پول</h4>
            <p class="text-xs text-on-surface-variant leading-relaxed">پس از تکمیل سفارش، مبلغ پورسانت به صورت خودکار به کیف پول شما واریز می‌شود.</p>
        </div>
    </div>
</div>

<script>
function copyAffiliateLink() {
    const linkText = document.getElementById('affiliate-link-text').innerText;
    navigator.clipboard.writeText(linkText).then(() => {
        const btn = document.getElementById('btn-copy-aff');
        const oldHtml = btn.innerHTML;
        btn.innerHTML = '<span class="material-symbols-outlined text-lg">check</span><span>کپی شد!</span>';
        btn.classList.replace('bg-primary', 'bg-emerald-600');
        setTimeout(() => {
            btn.innerHTML = oldHtml;
            btn.classList.replace('bg-emerald-600', 'bg-primary');
        }, 2500);
    });
}
</script>
