<?php
if (!defined('ABSPATH')) exit;
$status = isset($_GET['status']) ? sanitize_key($_GET['status']) : 'all';
$orders = Serene_Panel_Orders::get_user_orders($user_id, $status, 10);
$options = get_option('serene_panel_options', []);
$enable_rma = !empty($options['enable_rma']);
$enable_tracking = !empty($options['enable_post_tracking']);
?>
<div class="space-y-6 font-body">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-on-surface">مدیریت سفارشات</h1>
            <p class="text-xs md:text-sm text-on-surface-variant mt-1">تاریخچه خریدها، فاکتورها، رهگیری مرسولات و درخواست‌های مرجوعی خود را مشاهده کنید.</p>
        </div>
        
        <!-- Status Filter Tabs -->
        <div class="flex gap-2 overflow-x-auto pb-1 max-w-full">
            <a href="?tab=orders&status=all" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?php echo $status === 'all' ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'; ?>">همه</a>
            <a href="?tab=orders&status=completed" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?php echo $status === 'completed' ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'; ?>">تکمیل شده</a>
            <a href="?tab=orders&status=processing" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?php echo $status === 'processing' ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'; ?>">در حال پردازش</a>
            <a href="?tab=orders&status=on-hold" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?php echo $status === 'on-hold' ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'; ?>">در انتظار پرداخت کارت‌به‌کارت</a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <?php if (empty($orders->orders)): ?>
            <div class="text-center py-16 space-y-4">
                <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center mx-auto text-on-surface-variant">
                    <span class="material-symbols-outlined text-3xl">shopping_bag</span>
                </div>
                <h3 class="text-base font-bold text-on-surface">سفارشی یافت نشد</h3>
                <p class="text-xs text-on-surface-variant max-w-sm mx-auto">شما هنوز سفارشی در این وضعیت ثبت نکرده‌اید.</p>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md">رفتن به فروشگاه</a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b border-outline-variant/20 bg-surface-container-low/50 text-xs font-bold text-on-surface-variant">
                            <th class="p-4">شماره سفارش</th>
                            <th class="p-4">تاریخ ثبت</th>
                            <th class="p-4">وضعیت</th>
                            <th class="p-4">مبلغ کل</th>
                            <th class="p-4 text-left">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10 text-sm">
                        <?php foreach ($orders->orders as $order): 
                            $badge = Serene_Panel_Orders::get_status_badge($order->get_status());
                            $tracking_info = Serene_Panel_Post_Tracker::get_order_tracking_info($order->get_id());
                            $tracking_code = $tracking_info['tracking_code'];
                            $order_status = $order->get_status();
                        ?>
                        <tr class="hover:bg-surface-container-low/40 transition-colors">
                            <td class="p-4 font-bold text-primary font-mono">#<?php echo esc_html($order->get_order_number()); ?></td>
                            <td class="p-4 text-xs text-on-surface-variant"><?php echo esc_html(date_i18n('Y/m/d', strtotime($order->get_date_created()))); ?></td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border <?php echo esc_attr($badge['class']); ?>">
                                    <?php echo esc_html($badge['label']); ?>
                                </span>
                            </td>
                            <td class="p-4 font-bold text-on-surface"><?php echo wc_price($order->get_total()); ?></td>
                            <td class="p-4 text-left space-x-2 space-x-reverse whitespace-nowrap">
                                <?php if ($order_status === 'on-hold'): ?>
                                    <button onclick="openC2CModal(<?php echo $order->get_id(); ?>, '<?php echo $order->get_total(); ?>')" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer">ثبت فیش کارت‌به‌کارت</button>
                                <?php endif; ?>

                                <?php if ($tracking_code && $enable_tracking): ?>
                                    <button onclick="openTrackingModal('<?php echo esc_attr($tracking_code); ?>', '<?php echo esc_attr($tracking_info['carrier_name']); ?>', '<?php echo esc_attr($tracking_info['tracking_url']); ?>')" class="bg-sky-50 text-sky-700 hover:bg-sky-100 px-3 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1 cursor-pointer">
                                        <span class="material-symbols-outlined text-sm"><?php echo esc_html($tracking_info['carrier_icon'] ?? 'local_shipping'); ?></span>
                                        <span>رهگیری مرسوله</span>
                                    </button>
                                <?php endif; ?>

                                <?php if ($order_status === 'completed' && $enable_rma): ?>
                                    <button onclick="openRMAModal(<?php echo $order->get_id(); ?>, '#<?php echo esc_attr($order->get_order_number()); ?>')" class="bg-rose-50 text-rose-700 hover:bg-rose-100 px-3 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1 cursor-pointer">
                                        <span class="material-symbols-outlined text-sm">assignment_return</span>
                                        <span>مرجوعی</span>
                                    </button>
                                <?php endif; ?>

                                <a href="<?php echo esc_url(add_query_arg(['serene_invoice' => $order->get_id()], home_url('/'))); ?>" target="_blank" class="bg-surface-container hover:bg-surface-container-high text-on-surface px-3 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">receipt</span>
                                    <span>فاکتور PDF</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for C2C Manual Submission -->
<div id="c2c-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest max-w-md w-full rounded-3xl p-6 shadow-2xl border border-outline-variant/30 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-outline-variant/20">
            <h3 class="font-bold text-base text-on-surface">ثبت اطلاعات کارت‌به‌کارت</h3>
            <button onclick="closeC2CModal()" class="material-symbols-outlined text-on-surface-variant cursor-pointer">close</button>
        </div>
        <form id="c2c-submit-form" class="space-y-4">
            <input type="hidden" id="c2c-order-id" name="order_id" value="">
            <input type="hidden" id="c2c-amount" name="amount" value="">
            
            <div class="bg-primary-container/20 p-4 rounded-2xl text-xs space-y-1 text-primary">
                <div>شماره کارت مقصد: <strong class="font-mono font-bold select-all"><?php echo esc_html(get_option('serene_panel_options', [])['c2c_card_number'] ?? '6037997100000000'); ?></strong></div>
                <div>به نام: <strong><?php echo esc_html(get_option('serene_panel_options', [])['c2c_card_holder'] ?? 'مدیریت'); ?></strong></div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">کد پیگیری / شماره ارجاع بانکی</label>
                <input type="text" name="tracking_code" required class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm text-left" dir="ltr" placeholder="12345678">
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">۴ رقم آخر شماره کارت واریزکننده</label>
                <input type="text" name="card_number" maxlength="4" required class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm text-left" dir="ltr" placeholder="5432">
            </div>

            <button type="button" onclick="submitC2CTransfer()" class="w-full bg-primary hover:bg-primary-dim text-white py-3.5 rounded-xl font-bold text-sm shadow-md cursor-pointer">
                ثبت و تایید واریز
            </button>
        </form>
    </div>
</div>

<!-- Modal for Post Tracking -->
<div id="tracking-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest max-w-md w-full rounded-3xl p-6 shadow-2xl border border-outline-variant/30 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-outline-variant/20">
            <h3 class="font-bold text-base text-on-surface">رهگیری مرسوله پستی</h3>
            <button onclick="closeTrackingModal()" class="material-symbols-outlined text-on-surface-variant cursor-pointer">close</button>
        </div>
        <div class="space-y-4 text-center">
            <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-2xl">local_shipping</span>
            </div>
            <div class="text-xs text-on-surface-variant">کد رهگیری پستی مرسوله شما:</div>
            <div id="tracking-code-display" class="font-mono text-lg font-black text-primary select-all bg-surface-container-low p-3 rounded-2xl border border-outline-variant/30" dir="ltr"></div>
            <a id="tracking-post-link" href="#" target="_blank" class="w-full bg-primary hover:bg-primary-dim text-white py-3.5 rounded-xl font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                <span>مشاهده در سامانه رهگیری شرکت پست</span>
                <span class="material-symbols-outlined text-base">open_in_new</span>
            </a>
        </div>
    </div>
</div>

<!-- Modal for RMA Return Request -->
<div id="rma-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest max-w-md w-full rounded-3xl p-6 shadow-2xl border border-outline-variant/30 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-outline-variant/20">
            <h3 class="font-bold text-base text-on-surface">درخواست مرجوعی کالا</h3>
            <button onclick="closeRMAModal()" class="material-symbols-outlined text-on-surface-variant cursor-pointer">close</button>
        </div>
        <form id="rma-submit-form" class="space-y-4">
            <input type="hidden" id="rma-order-id" name="order_id" value="">
            <div class="text-xs text-on-surface-variant">
                سفارش انتخاب شده: <strong id="rma-order-num" class="text-primary font-mono"></strong>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">علت و توضیحات درخواست مرجوعی</label>
                <textarea name="reason" id="rma-reason" required rows="4" placeholder="لطفاً علت درخواست مرجوعی و مشخصات کالای مورد نظر را بنویسید..." class="w-full bg-surface-container-low border-none rounded-xl p-3 text-xs"></textarea>
            </div>

            <button type="button" onclick="submitRMARequest()" class="w-full bg-rose-600 hover:bg-rose-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md cursor-pointer">
                ثبت و ارسال درخواست مرجوعی
            </button>
        </form>
    </div>
</div>

<script>
function openC2CModal(orderId, amount) {
    document.getElementById('c2c-order-id').value = orderId;
    document.getElementById('c2c-amount').value = amount;
    document.getElementById('c2c-modal').classList.remove('hidden');
}
function closeC2CModal() {
    document.getElementById('c2c-modal').classList.add('hidden');
}
function submitC2CTransfer() {
    const form = document.getElementById('c2c-submit-form');
    const formData = new FormData(form);
    formData.append('action', 'serene_c2c_submit');
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        alert(d.data.message);
        if (d.success) location.reload();
    });
}

function openTrackingModal(trackingCode, carrierName, trackingUrl) {
    document.getElementById('tracking-code-display').innerText = trackingCode;
    const linkEl = document.getElementById('tracking-post-link');
    if (trackingUrl) {
        linkEl.href = trackingUrl;
        linkEl.style.display = 'flex';
        linkEl.querySelector('span').innerText = 'مشاهده در سامانه رهگیری ' + (carrierName || 'پست');
    } else {
        linkEl.style.display = 'none';
    }
    document.getElementById('tracking-modal').classList.remove('hidden');
}
function closeTrackingModal() {
    document.getElementById('tracking-modal').classList.add('hidden');
}

function openRMAModal(orderId, orderNum) {
    document.getElementById('rma-order-id').value = orderId;
    document.getElementById('rma-order-num').innerText = orderNum;
    document.getElementById('rma-modal').classList.remove('hidden');
}
function closeRMAModal() {
    document.getElementById('rma-modal').classList.add('hidden');
}
function submitRMARequest() {
    const orderId = document.getElementById('rma-order-id').value;
    const reason = document.getElementById('rma-reason').value.trim();
    if (!reason) return alert('لطفاً علت درخواست مرجوعی را بنویسید.');

    const formData = new FormData();
    formData.append('action', 'serene_create_rma');
    formData.append('order_id', orderId);
    formData.append('reason', reason);
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        alert(d.data.message);
        if (d.success) closeRMAModal();
    });
}
</script>
