<?php
if (!defined('ABSPATH')) exit;

$stats = Serene_Panel_Tickets::get_ticket_stats();
$departments = Serene_Panel_Tickets::get_departments();
$current_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
$current_dept   = isset($_GET['department']) ? sanitize_text_field($_GET['department']) : 'all';
$search         = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

$tickets = Serene_Panel_Tickets::get_all_tickets(100, $current_status, $current_dept, $search);
?>
<div class="wrap font-body text-right" dir="rtl" style="margin-top: 20px;">
    <!-- In-Page Alert Container -->
    <div id="admin-notif-toast" class="hidden fixed bottom-6 left-6 z-50 p-4 rounded-2xl shadow-2xl text-xs font-bold transition-all duration-300 max-w-sm flex items-center gap-3"></div>

    <div class="space-y-6 max-w-7xl mx-auto pb-12">
        <!-- Header Banner -->
        <div class="bg-gradient-to-l from-primary to-primary-dim rounded-3xl p-8 text-white shadow-xl flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs font-bold backdrop-blur-sm">
                    <span class="material-symbols-outlined text-sm">support_agent</span>
                    <span>مرکز پشتیبانی و تیکتینگ پالت پنل</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black">مدیریت تیکت‌ها و پیام‌های کاربران</h1>
                <p class="text-white/80 text-xs md:text-sm">پاسخگویی به کاربران، مدیریت دپارتمان‌ها، تغییر وضعیت و اولویت تیکت‌ها در لحظه</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="button" onclick="openDepartmentsModal()" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer backdrop-blur-sm">
                    <span class="material-symbols-outlined text-base">domain</span>
                    <span>مدیریت دپارتمان‌ها (<?php echo count($departments); ?>)</span>
                </button>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs text-on-surface-variant">کل تیکت‌ها</div>
                    <div class="text-2xl font-black text-on-surface mt-1"><?php echo $stats['total']; ?></div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">forum</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs text-amber-700 font-bold">در انتظار پاسخ</div>
                    <div class="text-2xl font-black text-amber-600 mt-1"><?php echo $stats['pending']; ?></div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">pending</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs text-emerald-700 font-bold">پاسخ داده شده</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1"><?php echo $stats['replied']; ?></div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">mark_chat_read</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs text-on-surface-variant">تیکت‌های بسته شده</div>
                    <div class="text-2xl font-black text-on-surface mt-1"><?php echo $stats['closed']; ?></div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-surface-container text-on-surface-variant flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">lock</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-surface-container-lowest p-5 rounded-3xl border border-outline-variant/30 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <!-- Status Tabs -->
            <div class="flex flex-wrap gap-2 text-xs">
                <?php
                $status_options = [
                    'all'         => 'همه تیکت‌ها',
                    'pending'     => 'در انتظار پاسخ',
                    'open'        => 'جدید',
                    'in_progress' => 'در حال بررسی',
                    'replied'     => 'پاسخ داده شده',
                    'closed'      => 'بسته شده',
                ];
                foreach ($status_options as $key => $label):
                    $active = ($current_status === $key);
                ?>
                    <a href="<?php echo esc_url(add_query_arg(['page' => 'palette-tickets', 'status' => $key, 'department' => $current_dept], admin_url('admin.php'))); ?>" class="px-3.5 py-2 rounded-xl font-bold transition-all <?php echo $active ? 'bg-primary text-white shadow-sm' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high'; ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Department & Search Filter -->
            <form method="GET" action="<?php echo esc_url(admin_url('admin.php')); ?>" class="flex items-center gap-3 w-full md:w-auto">
                <input type="hidden" name="page" value="palette-tickets">
                <input type="hidden" name="status" value="<?php echo esc_attr($current_status); ?>">
                
                <select name="department" onchange="this.form.submit()" class="bg-surface-container-low border-none rounded-xl p-2.5 text-xs text-on-surface font-bold">
                    <option value="all">همه دپارتمان‌ها</option>
                    <?php foreach ($departments as $slug => $dname): ?>
                        <option value="<?php echo esc_attr($slug); ?>" <?php selected($current_dept, $slug); ?>><?php echo esc_html($dname); ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="relative flex items-center">
                    <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="جستجوی موضوع یا شماره تیکت..." class="bg-surface-container-low border-none rounded-xl p-2.5 pr-8 text-xs w-48 md:w-56">
                    <span class="material-symbols-outlined absolute right-2.5 text-on-surface-variant text-base">search</span>
                </div>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <?php if (empty($tickets)): ?>
                <div class="text-center py-16 space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-surface-container flex items-center justify-center mx-auto text-on-surface-variant">
                        <span class="material-symbols-outlined text-3xl">inbox</span>
                    </div>
                    <h4 class="text-sm font-bold text-on-surface">هیچ تیکتی با این مشخصات یافت نشد.</h4>
                    <p class="text-xs text-on-surface-variant">می‌توانید فیلترها را تغییر داده یا جستجوی جدیدی انجام دهید.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-xs">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant font-bold border-b border-outline-variant/20">
                                <th class="p-4">شناسه</th>
                                <th class="p-4">کاربر ارسال‌کننده</th>
                                <th class="p-4">موضوع تیکت</th>
                                <th class="p-4">دپارتمان</th>
                                <th class="p-4 text-center">اولویت</th>
                                <th class="p-4 text-center">وضعیت</th>
                                <th class="p-4">آخرین به‌روزرسانی</th>
                                <th class="p-4 text-left">عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/20">
                            <?php foreach ($tickets as $t): ?>
                            <tr class="hover:bg-surface-container-low/50 transition-colors" id="ticket-row-<?php echo $t->id; ?>">
                                <td class="p-4 font-mono font-black text-primary">#<?php echo $t->id; ?></td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2.5">
                                        <img src="<?php echo esc_url($t->user_avatar); ?>" class="w-8 h-8 rounded-full object-cover shrink-0">
                                        <div>
                                            <div class="font-bold text-on-surface"><?php echo esc_html($t->user_name); ?></div>
                                            <div class="text-[10px] text-on-surface-variant font-mono"><?php echo esc_html($t->user_phone ?: $t->user_email); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-on-surface"><?php echo esc_html($t->subject); ?></div>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-surface-container text-on-surface-variant text-[11px] font-bold">
                                        <?php echo esc_html($t->department_name); ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?php echo esc_attr($t->priority_info['class']); ?>">
                                        <?php echo esc_html($t->priority_info['label']); ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center" id="ticket-status-badge-<?php echo $t->id; ?>">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?php echo esc_attr($t->status_info['class']); ?>">
                                        <?php echo esc_html($t->status_info['label']); ?>
                                    </span>
                                </td>
                                <td class="p-4 text-[11px] text-on-surface-variant">
                                    <?php echo esc_html(date_i18n('Y/m/d H:i', strtotime($t->updated_at))); ?>
                                </td>
                                <td class="p-4 text-left space-x-2 space-x-reverse whitespace-nowrap">
                                    <button type="button" onclick="openTicketChat(<?php echo $t->id; ?>)" class="bg-primary text-white hover:bg-primary-dim px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-sm cursor-pointer">
                                        <span class="material-symbols-outlined text-sm">chat</span>
                                        <span>مشاهده و پاسخ</span>
                                    </button>
                                    <button type="button" onclick="deleteTicket(<?php echo $t->id; ?>)" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer" title="حذف تیکت">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Ticket Chat Modal -->
<div id="admin-ticket-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest max-w-4xl w-full rounded-3xl shadow-2xl border border-outline-variant/30 flex flex-col max-h-[90vh] overflow-hidden animate-scale-up">
        
        <!-- Modal Header -->
        <div class="p-6 border-b border-outline-variant/20 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-surface-container-low/50">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono font-black text-primary text-sm" id="modal-ticket-id">#--</span>
                    <h3 class="text-base font-black text-on-surface" id="modal-ticket-subject">عنوان تیکت</h3>
                </div>
                <div class="text-xs text-on-surface-variant flex items-center gap-3">
                    <span id="modal-ticket-user">کاربر: --</span>
                    <span>•</span>
                    <span id="modal-ticket-dept">دپارتمان: --</span>
                </div>
            </div>

            <!-- Status & Priority Changers -->
            <div class="flex items-center gap-2">
                <select id="modal-status-select" onchange="changeTicketStatus()" class="bg-white border-none rounded-xl p-2 text-xs font-bold shadow-sm">
                    <option value="open">جدید / باز</option>
                    <option value="pending">در انتظار پاسخ</option>
                    <option value="in_progress">در حال بررسی</option>
                    <option value="replied">پاسخ داده شده</option>
                    <option value="closed">بسته شده</option>
                </select>

                <select id="modal-priority-select" onchange="changeTicketPriority()" class="bg-white border-none rounded-xl p-2 text-xs font-bold shadow-sm">
                    <option value="low">اولویت کم</option>
                    <option value="medium">اولویت متوسط</option>
                    <option value="high">اولویت فوری</option>
                </select>

                <button type="button" onclick="closeTicketModal()" class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high p-2 rounded-xl cursor-pointer">
                    close
                </button>
            </div>
        </div>

        <!-- Messages Thread -->
        <div id="modal-ticket-messages" class="flex-1 p-6 space-y-4 overflow-y-auto bg-surface-container-lowest/50 text-xs">
            <div class="text-center py-12 text-on-surface-variant">در حال بارگذاری گفتگو...</div>
        </div>

        <!-- Admin Reply Box -->
        <div class="p-5 border-t border-outline-variant/20 bg-surface-container-low space-y-3">
            <input type="hidden" id="modal-active-ticket-id" value="">
            <textarea id="modal-reply-text" rows="3" placeholder="متن پاسخ پشتیبانی خود را بنویسید..." class="w-full bg-white border-none rounded-2xl p-4 text-xs resize-none shadow-inner focus:ring-2 focus:ring-primary"></textarea>
            
            <div class="flex justify-between items-center">
                <label class="flex items-center gap-2 text-xs text-on-surface-variant cursor-pointer">
                    <input type="checkbox" id="modal-close-after-reply" class="rounded text-primary">
                    <span>بستن تیکت پس از ارسال پاسخ</span>
                </label>

                <button type="button" onclick="sendAdminTicketReply()" id="btn-send-ticket-reply" class="bg-primary hover:bg-primary-dim text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-base">send</span>
                    <span>ارسال پاسخ به کاربر</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Departments Modal -->
<div id="admin-departments-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest max-w-lg w-full rounded-3xl p-6 shadow-2xl border border-outline-variant/30 space-y-6">
        <div class="flex justify-between items-center pb-3 border-b border-outline-variant/20">
            <h3 class="font-bold text-base text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">domain</span>
                <span>مدیریت دپارتمان‌ها و واحدهای پشتیبانی</span>
            </h3>
            <button onclick="closeDepartmentsModal()" class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high p-1.5 rounded-xl cursor-pointer">close</button>
        </div>

        <div class="space-y-4">
            <!-- Add Department Row -->
            <div class="bg-surface-container-low p-4 rounded-2xl space-y-3">
                <div class="text-xs font-bold text-on-surface">افزودن دپارتمان جدید</div>
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" id="new-dept-slug" placeholder="شناسه انگلیسی (مثال: crm)" class="bg-white border-none rounded-xl p-2.5 text-xs text-left" dir="ltr">
                    <input type="text" id="new-dept-title" placeholder="عنوان دپارتمان (مثال: واحد ارتباط با مشتریان)" class="bg-white border-none rounded-xl p-2.5 text-xs">
                </div>
                <button type="button" onclick="addNewDepartment()" class="w-full bg-primary hover:bg-primary-dim text-white py-2 rounded-xl text-xs font-bold shadow-sm cursor-pointer">
                    ➕ ثبت دپارتمان جدید
                </button>
            </div>

            <!-- Existing Departments List -->
            <div class="space-y-2 max-h-60 overflow-y-auto" id="departments-list-container">
                <?php foreach ($departments as $slug => $title): ?>
                <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-xl text-xs dept-item-row" data-slug="<?php echo esc_attr($slug); ?>" data-title="<?php echo esc_attr($title); ?>">
                    <div>
                        <div class="font-bold text-on-surface"><?php echo esc_html($title); ?></div>
                        <div class="text-[10px] text-on-surface-variant font-mono"><?php echo esc_html($slug); ?></div>
                    </div>
                    <button type="button" onclick="deleteDepartment('<?php echo esc_js($slug); ?>')" class="text-rose-500 hover:text-rose-700 material-symbols-outlined text-base cursor-pointer">
                        delete
                    </button>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="button" onclick="saveDepartmentsList()" id="btn-save-departments" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-2xl text-xs font-bold shadow-md cursor-pointer flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined text-base">check</span>
                <span>ذخیره نهایی دپارتمان‌ها</span>
            </button>
        </div>
    </div>
</div>

<script>
const ticketsAjax = {
    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
    nonce: '<?php echo wp_create_nonce('serene_panel_nonce'); ?>'
};

function showToast(msg, type = 'success') {
    const toast = document.getElementById('admin-notif-toast');
    if (!toast) return;
    toast.className = 'fixed bottom-6 left-6 z-50 p-4 rounded-2xl shadow-2xl text-xs font-bold transition-all duration-300 max-w-sm flex items-center gap-3 ' + 
        (type === 'success' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white');
    toast.innerHTML = `<span class="material-symbols-outlined text-lg">${type === 'success' ? 'check_circle' : 'error'}</span><span>${msg}</span>`;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 4000);
}

function openTicketChat(ticketId) {
    document.getElementById('modal-active-ticket-id').value = ticketId;
    document.getElementById('modal-ticket-id').innerText = '#' + ticketId;
    document.getElementById('admin-ticket-modal').classList.remove('hidden');
    
    const msgBox = document.getElementById('modal-ticket-messages');
    msgBox.innerHTML = '<div class="text-center py-12 text-on-surface-variant">در حال بارگذاری پیام‌ها...</div>';

    const fd = new FormData();
    fd.append('action', 'serene_admin_get_ticket_details');
    fd.append('ticket_id', ticketId);
    fd.append('nonce', ticketsAjax.nonce);

    fetch(ticketsAjax.url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            msgBox.innerHTML = '<div class="text-center py-12 text-rose-600">خطا در بارگذاری اطلاعات تیکت.</div>';
            return;
        }

        const t = data.data.ticket;
        const msgs = data.data.messages;

        document.getElementById('modal-ticket-subject').innerText = t.subject;
        document.getElementById('modal-ticket-user').innerText = 'کاربر: ' + (t.user_name || 'مشتری');
        document.getElementById('modal-ticket-dept').innerText = 'دپارتمان: ' + (t.department_name || t.department);
        document.getElementById('modal-status-select').value = t.status;
        document.getElementById('modal-priority-select').value = t.priority;

        let html = '';
        msgs.forEach(m => {
            const isAdmin = m.is_admin == 1;
            html += `
            <div class="flex gap-3 ${isAdmin ? 'flex-row' : 'flex-row-reverse'}">
                <img src="${m.sender_avatar || ''}" class="w-8 h-8 rounded-full object-cover shrink-0 mt-1">
                <div class="max-w-xl space-y-1">
                    <div class="flex items-center gap-2 ${isAdmin ? 'justify-start' : 'justify-end'}">
                        <span class="font-bold text-[11px] text-on-surface">${m.sender_name}</span>
                        <span class="text-[10px] text-on-surface-variant">${m.created_at}</span>
                    </div>
                    <div class="p-4 rounded-2xl ${isAdmin ? 'bg-primary text-white rounded-tr-none' : 'bg-surface-container-high text-on-surface rounded-tl-none'} shadow-sm leading-relaxed text-xs whitespace-pre-wrap">
                        ${m.message}
                    </div>
                </div>
            </div>`;
        });

        msgBox.innerHTML = html;
        msgBox.scrollTop = msgBox.scrollHeight;
    })
    .catch(() => {
        msgBox.innerHTML = '<div class="text-center py-12 text-rose-600">خطای ارتباط با سرور.</div>';
    });
}

function closeTicketModal() {
    document.getElementById('admin-ticket-modal').classList.add('hidden');
}

function sendAdminTicketReply() {
    const ticketId = document.getElementById('modal-active-ticket-id').value;
    const msg = document.getElementById('modal-reply-text').value.trim();
    const closeAfter = document.getElementById('modal-close-after-reply').checked;
    const btn = document.getElementById('btn-send-ticket-reply');

    if (!msg) return showToast('لطفاً متن پاسخ را وارد نمایید.', 'error');

    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-base">sync</span><span>در حال ارسال...</span>';

    const fd = new FormData();
    fd.append('action', 'serene_admin_reply_ticket');
    fd.append('ticket_id', ticketId);
    fd.append('message', msg);
    fd.append('close_ticket', closeAfter ? 1 : 0);
    fd.append('nonce', ticketsAjax.nonce);

    fetch(ticketsAjax.url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-base">send</span><span>ارسال پاسخ به کاربر</span>';
        if (data.success) {
            document.getElementById('modal-reply-text').value = '';
            showToast(data.data.message || 'پاسخ با موفقیت ارسال شد.', 'success');
            openTicketChat(ticketId);
        } else {
            showToast(data.data.message || 'خطا در ارسال پاسخ.', 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-base">send</span><span>ارسال پاسخ به کاربر</span>';
        showToast('خطای ارتباط با سرور.', 'error');
    });
}

function changeTicketStatus() {
    const ticketId = document.getElementById('modal-active-ticket-id').value;
    const status = document.getElementById('modal-status-select').value;

    const fd = new FormData();
    fd.append('action', 'serene_admin_update_ticket_status');
    fd.append('ticket_id', ticketId);
    fd.append('status', status);
    fd.append('nonce', ticketsAjax.nonce);

    fetch(ticketsAjax.url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if (d.success) showToast(d.data.message, 'success');
    });
}

function changeTicketPriority() {
    const ticketId = document.getElementById('modal-active-ticket-id').value;
    const priority = document.getElementById('modal-priority-select').value;

    const fd = new FormData();
    fd.append('action', 'serene_admin_update_ticket_priority');
    fd.append('ticket_id', ticketId);
    fd.append('priority', priority);
    fd.append('nonce', ticketsAjax.nonce);

    fetch(ticketsAjax.url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if (d.success) showToast(d.data.message, 'success');
    });
}

function deleteTicket(ticketId) {
    if (!confirm('آیا از حذف کامل این تیکت و پیام‌های آن اطمینان دارید؟')) return;

    const fd = new FormData();
    fd.append('action', 'serene_admin_delete_ticket');
    fd.append('ticket_id', ticketId);
    fd.append('nonce', ticketsAjax.nonce);

    fetch(ticketsAjax.url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.data.message, 'success');
            const row = document.getElementById('ticket-row-' + ticketId);
            if (row) row.remove();
        } else {
            showToast(d.data.message || 'خطا در حذف تیکت.', 'error');
        }
    });
}

// Departments Modal logic
function openDepartmentsModal() {
    document.getElementById('admin-departments-modal').classList.remove('hidden');
}

function closeDepartmentsModal() {
    document.getElementById('admin-departments-modal').classList.add('hidden');
}

function addNewDepartment() {
    const slug = document.getElementById('new-dept-slug').value.trim().toLowerCase().replace(/[^a-z0-9_]/g, '');
    const title = document.getElementById('new-dept-title').value.trim();

    if (!slug || !title) return showToast('لطفاً شناسه و عنوان دپارتمان را وارد کنید.', 'error');

    const container = document.getElementById('departments-list-container');
    const div = document.createElement('div');
    div.className = 'flex items-center justify-between p-3 bg-surface-container-low rounded-xl text-xs dept-item-row';
    div.setAttribute('data-slug', slug);
    div.setAttribute('data-title', title);
    div.innerHTML = `
        <div>
            <div class="font-bold text-on-surface">${title}</div>
            <div class="text-[10px] text-on-surface-variant font-mono">${slug}</div>
        </div>
        <button type="button" onclick="this.closest('.dept-item-row').remove()" class="text-rose-500 hover:text-rose-700 material-symbols-outlined text-base cursor-pointer">
            delete
        </button>`;
    container.appendChild(div);

    document.getElementById('new-dept-slug').value = '';
    document.getElementById('new-dept-title').value = '';
}

function deleteDepartment(slug) {
    const row = document.querySelector(`.dept-item-row[data-slug="${slug}"]`);
    if (row) row.remove();
}

function saveDepartmentsList() {
    const rows = document.querySelectorAll('.dept-item-row');
    const depts = {};
    rows.forEach(r => {
        const s = r.getAttribute('data-slug');
        const t = r.getAttribute('data-title');
        if (s && t) depts[s] = t;
    });

    const fd = new FormData();
    fd.append('action', 'serene_admin_save_departments');
    fd.append('departments', JSON.stringify(depts));
    fd.append('nonce', ticketsAjax.nonce);

    fetch(ticketsAjax.url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.data.message || 'دپارتمان‌ها با موفقیت ذخیره شدند.', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('خطا در ذخیره دپارتمان‌ها.', 'error');
        }
    });
}
</script>
