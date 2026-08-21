<?php
if (!defined('ABSPATH')) exit;
$ticket = Serene_Panel_Tickets::get_ticket($ticket_id, $user_id);
if (!$ticket) {
    echo '<p class="text-error">تیکت یافت نشد.</p>';
    return;
}
$messages = Serene_Panel_Tickets::get_ticket_messages($ticket_id);
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="?tab=tickets" class="w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center text-on-surface hover:bg-primary hover:text-white transition-all">
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
            <div>
                <h1 class="text-xl font-black text-on-surface"><?php echo esc_html($ticket->subject); ?></h1>
                <div class="text-xs text-on-surface-variant">شناسه تیکت: #<?php echo $ticket->id; ?></div>
            </div>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-sm p-6 space-y-4 min-h-[400px] flex flex-col justify-between">
        <div class="space-y-4 overflow-y-auto max-h-[500px] pr-2">
            <?php foreach ($messages as $m): 
                $is_me = ($m->sender_id == $user_id && !$m->is_admin);
            ?>
            <div class="flex flex-col <?php echo $is_me ? 'items-start' : 'items-end'; ?> space-y-1">
                <div class="max-w-lg p-4 rounded-2xl text-sm leading-relaxed <?php echo $is_me ? 'bg-primary text-white rounded-br-none' : 'bg-surface-container-low text-on-surface rounded-bl-none border border-outline-variant/20'; ?>">
                    <?php echo nl2br(esc_html($m->message)); ?>
                </div>
                <span class="text-[10px] text-on-surface-variant px-1"><?php echo date_i18n('H:i - Y/m/d', strtotime($m->created_at)); ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Reply Input -->
        <div class="pt-4 border-t border-outline-variant/20 flex gap-3">
            <textarea id="reply-ticket-msg" rows="2" placeholder="پاسخ خود را بنویسید..." class="flex-1 bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary"></textarea>
            <button onclick="replyTicket(<?php echo $ticket->id; ?>)" class="bg-primary hover:bg-primary-dim text-white px-6 rounded-xl font-bold text-sm flex items-center justify-center gap-2 shadow-md">
                <span class="material-symbols-outlined text-lg">send</span>
                <span>ارسال</span>
            </button>
        </div>
    </div>
</div>

<script>
function replyTicket(ticketId) {
    const msg = document.getElementById('reply-ticket-msg').value;
    if (!msg) return;

    const formData = new FormData();
    formData.append('action', 'serene_reply_ticket');
    formData.append('ticket_id', ticketId);
    formData.append('message', msg);
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        if (d.success) location.reload();
    });
}
</script>
