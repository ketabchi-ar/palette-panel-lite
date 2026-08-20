<?php
if (!defined('ABSPATH')) exit;
$ticket_id = isset($_GET['ticket_id']) ? intval($_GET['ticket_id']) : 0;

if ($ticket_id > 0) {
    include SERENE_PANEL_TEMPLATES_PATH . 'tickets/ticket-chat.php';
    return;
}

$tickets = Serene_Panel_Tickets::get_user_tickets($user_id);
$departments = Serene_Panel_Tickets::get_departments();
?>
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-on-surface">مرکز پیام و تیکت‌های پشتیبانی</h1>
            <p class="text-sm text-on-surface-variant mt-1">سوالات و درخواست‌های پشتیبانی خود را با کارشناسان ما مطرح کنید.</p>
        </div>
        <button onclick="document.getElementById('new-ticket-modal').classList.remove('hidden')" class="bg-primary text-white hover:bg-primary-dim px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-md shadow-primary/20">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>ارسال تیکت جدید</span>
        </button>
    </div>

    <!-- Tickets List -->
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <?php if (empty($tickets)): ?>
            <div class="text-center py-16 space-y-4">
                <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center mx-auto text-on-surface-variant">
                    <span class="material-symbols-outlined text-3xl">chat</span>
                </div>
                <h3 class="text-base font-bold text-on-surface">تیکتی ثبت نشده است</h3>
                <p class="text-xs text-on-surface-variant max-w-sm mx-auto">در صورت نیاز به راهنمایی یا پشتیبانی، یک تیکت جدید ایجاد کنید.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-outline-variant/10">
                <?php foreach ($tickets as $t): ?>
                <a href="?tab=tickets&ticket_id=<?php echo $t->id; ?>" class="flex items-center justify-between p-5 hover:bg-surface-container-low/40 transition-all">
                    <div class="space-y-1">
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-sm text-on-surface"><?php echo esc_html($t->subject); ?></span>
                            <span class="text-[11px] px-2.5 py-0.5 rounded-full font-bold <?php echo $t->status === 'replied' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700'; ?>">
                                <?php echo $t->status === 'replied' ? 'پاسخ داده شده' : 'در انتظار بررسی'; ?>
                            </span>
                        </div>
                        <div class="text-xs text-on-surface-variant flex items-center gap-4">
                            <span>دپارتمان: <?php echo esc_html($departments[$t->department] ?? $t->department); ?></span>
                            <span>•</span>
                            <span>آخرین به‌روزرسانی: <?php echo esc_html(date_i18n('Y/m/d H:i', strtotime($t->updated_at))); ?></span>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-on-surface-variant">chevron_left</span>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- New Ticket Modal -->
<div id="new-ticket-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest max-w-lg w-full rounded-3xl p-6 shadow-2xl border border-outline-variant/30 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-outline-variant/20">
            <h3 class="font-bold text-base text-on-surface">ارسال تیکت جدید</h3>
            <button onclick="document.getElementById('new-ticket-modal').classList.add('hidden')" class="material-symbols-outlined text-on-surface-variant">close</button>
        </div>
        <form id="create-ticket-form" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">موضوع تیکت</label>
                <input type="text" name="subject" required placeholder="عنوان درخواست خود را وارد کنید" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1">دپارتمان</label>
                    <select name="department" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-xs">
                        <?php foreach ($departments as $dk => $dv): ?>
                            <option value="<?php echo esc_attr($dk); ?>"><?php echo esc_html($dv); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1">اولویت</label>
                    <select name="priority" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-xs">
                        <option value="low">عادی</option>
                        <option value="medium" selected>متوسط</option>
                        <option value="high">فوری</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">متن پیام</label>
                <textarea name="message" rows="4" required placeholder="توضیحات کامل درخواست..." class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm"></textarea>
            </div>
            <button type="button" onclick="createTicket()" class="w-full bg-primary hover:bg-primary-dim text-white py-3.5 rounded-xl font-bold text-sm shadow-md">
                ثبت و ارسال تیکت
            </button>
        </form>
    </div>
</div>

<script>
function createTicket() {
    const form = document.getElementById('create-ticket-form');
    const formData = new FormData(form);
    formData.append('action', 'serene_create_ticket');
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        alert(d.data.message);
        if (d.success) location.href = '?tab=tickets&ticket_id=' + d.data.ticket_id;
    });
}
</script>
