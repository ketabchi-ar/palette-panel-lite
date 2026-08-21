<?php
if (!defined('ABSPATH')) exit;
$licenses = Serene_Panel_License_Manager::get_user_licenses($user_id);
$downloads = WC()->customer ? WC()->customer->get_downloadable_products() : [];
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-on-surface">دانلودها و کلیدهای لایسنس</h1>
        <p class="text-sm text-on-surface-variant mt-1">دسترسی امن به فایل‌های خریداری شده و کلیدهای فعالسازی.</p>
    </div>

    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-base text-on-surface">کلیدهای لایسنس فعال</h3>
        <div class="space-y-3">
            <?php foreach ($licenses as $lic): ?>
            <div class="p-4 rounded-2xl bg-surface-container-low flex flex-col md:flex-row justify-between items-start md:items-center gap-3 text-xs">
                <div>
                    <div class="font-bold text-on-surface"><?php echo esc_html($lic['product']); ?></div>
                    <div class="text-on-surface-variant mt-1">دامنه متصل: <code><?php echo esc_html($lic['domain']); ?></code></div>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto justify-between">
                    <code class="bg-white px-3 py-1.5 rounded-lg border border-outline-variant/30 font-bold select-all"><?php echo esc_html($lic['key']); ?></code>
                    <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full font-bold text-[10px]">فعال</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
