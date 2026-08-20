<?php
if (!defined('ABSPATH')) exit;
$txs = Serene_Panel_Wallet::get_transactions($user_id, 20);
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-on-surface">کیف پول و مدیریت تراکنش‌ها</h1>
        <p class="text-sm text-on-surface-variant mt-1">موجودی نقدی، پاداش‌های کش‌بک و انتقال اعتبار به سایر کاربران.</p>
    </div>

    <!-- Balance & Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-primary to-primary-dim text-white p-6 rounded-3xl shadow-xl flex flex-col justify-between">
            <div>
                <span class="text-xs text-white/80">موجودی کل قابل استفاده</span>
                <div class="text-3xl font-black mt-2"><?php echo number_format($wallet_balance); ?> <span class="text-xs font-normal text-white/70">تومان</span></div>
            </div>
            <div class="text-xs text-white/70 pt-4 mt-4 border-t border-white/10">امکان پرداخت فوری کلیه سفارشات</div>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-base text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">send_money</span>
                    <span>انتقال به کاربر دیگر</span>
                </h3>
                <p class="text-xs text-on-surface-variant mt-1">انتقال آنی موجودی با وارد کردن شماره همراه مقصد.</p>
            </div>
            <button onclick="document.getElementById('transfer-modal').classList.remove('hidden')" class="w-full bg-surface-container hover:bg-surface-container-high text-on-surface font-bold text-xs py-3 rounded-xl transition-all">
                انتقال موجودی
            </button>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-base text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">percent</span>
                    <span>کش‌بک فعال</span>
                </h3>
                <p class="text-xs text-on-surface-variant mt-1">درصد مشخصی از هر خرید موفق مجدداً به کیف پول شما بازگردانده می‌شود.</p>
            </div>
            <div class="text-sm font-black text-emerald-600"><?php echo esc_html(get_option('serene_panel_options', [])['cashback_percent'] ?? 5); ?>٪ پاداش نقدی</div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-base text-on-surface">گردش حساب و تراکنش‌های اخیر</h3>
        <?php if (empty($txs)): ?>
            <p class="text-xs text-on-surface-variant text-center py-8">تراکنشی ثبت نشده است.</p>
        <?php else: ?>
            <div class="divide-y divide-outline-variant/10 text-xs">
                <?php foreach ($txs as $tx): ?>
                <div class="py-3.5 flex justify-between items-center">
                    <div class="space-y-1">
                        <div class="font-bold text-on-surface"><?php echo esc_html($tx->description); ?></div>
                        <div class="text-on-surface-variant"><?php echo date_i18n('Y/m/d H:i', strtotime($tx->created_at)); ?></div>
                    </div>
                    <div class="text-left">
                        <div class="font-black text-sm <?php echo $tx->amount >= 0 ? 'text-emerald-600' : 'text-rose-600'; ?>" dir="ltr">
                            <?php echo ($tx->amount >= 0 ? '+' : '') . number_format($tx->amount) . ' T'; ?>
                        </div>
                        <div class="text-[11px] text-on-surface-variant">مانده: <?php echo number_format($tx->balance_after); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Transfer Modal -->
<div id="transfer-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest max-w-sm w-full rounded-3xl p-6 shadow-2xl border border-outline-variant/30 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-outline-variant/20">
            <h3 class="font-bold text-base text-on-surface">انتقال اعتبار کیف پول</h3>
            <button onclick="document.getElementById('transfer-modal').classList.add('hidden')" class="material-symbols-outlined text-on-surface-variant">close</button>
        </div>
        <form id="wallet-transfer-form" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">شماره موبایل گیرنده</label>
                <input type="tel" name="target_phone" required placeholder="09123456789" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm text-left" dir="ltr">
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">مبلغ انتقال (تومان)</label>
                <input type="number" name="amount" required placeholder="50000" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm text-left" dir="ltr">
            </div>
            <button type="button" onclick="submitWalletTransfer()" class="w-full bg-primary hover:bg-primary-dim text-white py-3.5 rounded-xl font-bold text-sm shadow-md">
                تایید و انتقال آنی
            </button>
        </form>
    </div>
</div>

<script>
function submitWalletTransfer() {
    const form = document.getElementById('wallet-transfer-form');
    const formData = new FormData(form);
    formData.append('action', 'serene_wallet_transfer');
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        alert(d.data.message);
        if (d.success) location.reload();
    });
}
</script>
