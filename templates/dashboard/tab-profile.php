<?php
if (!defined('ABSPATH')) exit;
$custom_fields = Serene_Panel_Form_Builder::get_custom_fields();
$two_fa_secret = get_user_meta($user_id, '_serene_2fa_secret', true);
$webauthn_keys = get_user_meta($user_id, '_serene_webauthn_keys', true) ?: [];
$sessions = Serene_Panel_Session_Manager::get_user_sessions($user_id);
$national_id = get_user_meta($user_id, '_serene_national_id', true);
$shahkar_verified = get_user_meta($user_id, '_serene_shahkar_verified', true);
?>
<div class="space-y-8 max-w-3xl mx-auto font-body">
    <div>
        <h1 class="text-2xl font-black text-on-surface">اطلاعات حساب و امنیت</h1>
        <p class="text-xs md:text-sm text-on-surface-variant mt-1">مشخصات فردی، تصویر آواتار، ورود دو مرحله‌ای و نشست‌های فعال حساب کاربری خود را مدیریت کنید.</p>
    </div>

    <!-- Personal Info Card -->
    <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl border border-outline-variant/30 shadow-sm space-y-6">
        <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">person</span>
            <span>مشخصات فردی</span>
        </h3>

        <!-- Avatar Section -->
        <div class="flex items-center gap-6 pb-6 border-b border-outline-variant/20">
            <div class="w-20 h-20 rounded-2xl overflow-hidden ring-4 ring-primary/20 bg-primary-container relative group">
                <img id="profile-avatar-preview" class="w-full h-full object-cover" src="<?php echo esc_url(get_avatar_url($user_id)); ?>" alt="Avatar"/>
            </div>
            <div class="space-y-2">
                <label class="cursor-pointer bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-bold px-4 py-2 rounded-xl transition-all inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">photo_camera</span>
                    <span>تغییر عکس پروفایل</span>
                    <input type="file" id="avatar-input" onchange="uploadAvatar()" class="hidden" accept="image/*">
                </label>
                <div class="text-[11px] text-on-surface-variant">فرمت‌های مجاز: JPG, PNG, WebP (حداکثر ۲ مگابایت)</div>
            </div>
        </div>

        <form id="profile-edit-form" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1">نام</label>
                    <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1">نام خانوادگی</label>
                    <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm font-bold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">آدرس ایمیل</label>
                <input type="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm text-left font-mono" dir="ltr">
            </div>

            <!-- Custom Form Builder Fields -->
            <?php 
            $active_fields = Serene_Panel_Form_Builder::get_active_fields();
            foreach ($active_fields as $f_key => $field): 
                $val = get_user_meta($user_id, $field['meta_key'] ?? ('_serene_' . $f_key), true);
                $is_req = !empty($field['required']);
            ?>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="text-xs font-bold text-on-surface-variant">
                        <?php echo esc_html($field['label']); ?>
                        <?php if ($is_req): ?><span class="text-error mr-1">*</span><?php endif; ?>
                    </label>
                    <?php if ($f_key === 'national_id' && $shahkar_verified): ?>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="material-symbols-outlined text-xs">verified</span>
                            <span>تایید شده با شاهکار</span>
                        </span>
                    <?php endif; ?>
                </div>
                <?php if (($field['type'] ?? 'text') === 'textarea'): ?>
                    <textarea name="<?php echo esc_attr($f_key); ?>" placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm" <?php echo $is_req ? 'required' : ''; ?>><?php echo esc_textarea($val); ?></textarea>
                <?php else: ?>
                    <input type="<?php echo esc_attr($field['type'] ?? 'text'); ?>" name="<?php echo esc_attr($f_key); ?>" value="<?php echo esc_attr($val); ?>" placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm" <?php echo $is_req ? 'required' : ''; ?>>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <div class="pt-4">
                <button type="button" onclick="saveProfile()" class="w-full bg-primary hover:bg-primary-dim text-white py-3.5 rounded-xl font-bold text-sm shadow-md transition-all cursor-pointer">
                    ذخیره تغییرات مشخصات
                </button>
            </div>
        </form>
    </div>

    <!-- Security & Auth Methods Card -->
    <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl border border-outline-variant/30 shadow-sm space-y-6">
        <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">security</span>
            <span>تنظیمات امنیت و روش‌های احراز هویت</span>
        </h3>

        <!-- 2FA Google Authenticator -->
        <div class="bg-surface-container-low p-5 rounded-2xl space-y-4">
            <div class="flex justify-between items-center flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">phonelink_lock</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-on-surface">ورود دو مرحله‌ای (Google Authenticator)</div>
                        <div class="text-[11px] text-on-surface-variant">تولید رمز یکبار مصرف امن با اپلیکیشن موبایل</div>
                    </div>
                </div>
                <div>
                    <?php if ($two_fa_secret): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            <span>فعال است</span>
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-200 text-slate-700 rounded-full text-xs font-bold">
                            غیرفعال
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($two_fa_secret): ?>
                <button type="button" onclick="disable2FA()" class="text-xs font-bold text-rose-600 hover:text-rose-800 transition-all flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">lock_open</span>
                    <span>غیرفعال‌سازی ورود دو مرحله‌ای</span>
                </button>
            <?php else: ?>
                <div id="2fa-setup-box" class="space-y-4">
                    <button type="button" onclick="start2FASetup()" id="btn-start-2fa" class="bg-primary hover:bg-primary-dim text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                        <span class="material-symbols-outlined text-base">qr_code_scanner</span>
                        <span>فعال‌سازی ورود دو مرحله‌ای</span>
                    </button>
                    
                    <!-- 2FA QR Code & Verify (Hidden initially) -->
                    <div id="2fa-qr-container" class="hidden bg-white p-5 rounded-2xl border border-outline-variant/40 space-y-4">
                        <div class="text-xs text-on-surface font-bold leading-relaxed">
                            ۱. بارکد زیر را با اپلیکیشن <strong>Google Authenticator</strong> اسکن کنید:
                        </div>
                        <div class="flex justify-center p-3 bg-slate-50 rounded-xl">
                            <img id="2fa-qr-img" src="" alt="2FA QR Code" class="w-48 h-48 rounded-lg shadow-sm">
                        </div>
                        <div class="text-[11px] text-on-surface-variant font-mono text-center select-all bg-slate-100 p-2 rounded-lg" dir="ltr" id="2fa-secret-text"></div>
                        <div class="space-y-2 pt-2 border-t border-outline-variant/20">
                            <div class="text-xs text-on-surface font-bold">۲. کد ۶ رقمی نمایش داده شده در اپلیکیشن را وارد کنید:</div>
                            <div class="flex gap-2">
                                <input type="text" id="2fa-verify-code" placeholder="123456" maxlength="6" class="w-36 bg-surface-container-low border-none rounded-xl p-2.5 text-center text-sm font-mono font-bold" dir="ltr">
                                <button type="button" onclick="confirm2FA()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all cursor-pointer">
                                    تایید و فعال‌سازی
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Biometrics & Passkeys -->
        <div class="bg-surface-container-low p-5 rounded-2xl space-y-4">
            <div class="flex justify-between items-center flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">fingerprint</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-on-surface">ورود بیومتریک (Touch ID / اثر انگشت / Face ID)</div>
                        <div class="text-[11px] text-on-surface-variant">ورود فوق سریع با کلید سخت‌افزاری دستگاه (Passkeys)</div>
                    </div>
                </div>
                <button type="button" onclick="registerBiometricDevice()" class="bg-primary hover:bg-primary-dim text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                    <span>ثبت اثر انگشت این دستگاه</span>
                </button>
            </div>

            <!-- List of Registered Keys -->
            <?php if (!empty($webauthn_keys)): ?>
                <div class="space-y-2 pt-2 border-t border-outline-variant/20">
                    <div class="text-[11px] font-bold text-on-surface-variant">دستگاه‌های بیومتریک ثبت شده:</div>
                    <?php foreach ($webauthn_keys as $k): ?>
                        <div class="bg-white p-3 rounded-xl flex items-center justify-between text-xs border border-outline-variant/30">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base text-primary">devices</span>
                                <span class="font-bold text-on-surface"><?php echo esc_html($k['name'] ?? 'کلید بیومتریک'); ?></span>
                                <span class="text-[10px] text-on-surface-variant font-mono"><?php echo esc_html($k['created_at'] ?? ''); ?></span>
                            </div>
                            <button type="button" onclick="deleteBiometricKey('<?php echo esc_attr($k['id']); ?>')" class="text-rose-500 hover:text-rose-700 material-symbols-outlined text-base">
                                delete
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Active Sessions Management -->
        <div class="bg-surface-container-low p-5 rounded-2xl space-y-4">
            <div class="flex justify-between items-center flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">devices_other</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-on-surface">مدیریت نشست‌های فعال (Active Sessions)</div>
                        <div class="text-[11px] text-on-surface-variant">دستگاه‌ها و مرورگرهایی که هم‌اکنون به حساب شما وارد هستند</div>
                    </div>
                </div>
                <?php if (count($sessions) > 1): ?>
                    <button type="button" onclick="revokeAllSessions()" class="text-xs font-bold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-xl transition-all">
                        خروج از سایر دستگاه‌ها
                    </button>
                <?php endif; ?>
            </div>

            <div class="space-y-2 pt-2 border-t border-outline-variant/20">
                <?php if (empty($sessions)): ?>
                    <div class="text-xs text-on-surface-variant text-center py-2">اطلاعات نشستی یافت نشد.</div>
                <?php else: ?>
                    <?php foreach ($sessions as $s): 
                        $is_current = isset($_COOKIE['serene_session_token']) && ($_COOKIE['serene_session_token'] === $s->session_token);
                    ?>
                        <div class="bg-white p-3.5 rounded-xl flex items-center justify-between text-xs border border-outline-variant/30 <?php echo $is_current ? 'ring-2 ring-primary/30' : ''; ?>">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-xl text-on-surface-variant">
                                    <?php echo (strpos($s->device_name, 'موبایل') !== false) ? 'smartphone' : 'laptop_mac'; ?>
                                </span>
                                <div>
                                    <div class="font-bold text-on-surface flex items-center gap-2">
                                        <span><?php echo esc_html($s->device_name); ?></span>
                                        <?php if ($is_current): ?>
                                            <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full">دستگاه فعلی شما</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[10px] text-on-surface-variant font-mono mt-0.5">
                                        IP: <?php echo esc_html($s->ip_address); ?> • آخرین فعالیت: <?php echo esc_html($s->last_activity); ?>
                                    </div>
                                </div>
                            </div>
                            <?php if (!$is_current): ?>
                                <button type="button" onclick="revokeSession(<?php echo (int) $s->id; ?>)" class="text-rose-500 hover:text-rose-700 text-xs font-bold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-base">logout</span>
                                    <span>خروج</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function saveProfile() {
    const form = document.getElementById('profile-edit-form');
    const formData = new FormData(form);
    formData.append('action', 'serene_update_profile');
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => alert(d.data.message));
}

function uploadAvatar() {
    const input = document.getElementById('avatar-input');
    if (!input.files[0]) return;

    const formData = new FormData();
    formData.append('action', 'serene_upload_avatar');
    formData.append('avatar', input.files[0]);
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            document.getElementById('profile-avatar-preview').src = d.data.url;
            alert(d.data.message);
        } else {
            alert(d.data.message);
        }
    });
}

function start2FASetup() {
    const formData = new FormData();
    formData.append('action', 'serene_2fa_setup');
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            document.getElementById('2fa-qr-img').src = d.data.qr_url;
            document.getElementById('2fa-secret-text').innerText = 'کلید متنی: ' + d.data.secret;
            document.getElementById('2fa-qr-container').classList.remove('hidden');
            document.getElementById('btn-start-2fa').classList.add('hidden');
        }
    });
}

function confirm2FA() {
    const code = document.getElementById('2fa-verify-code').value;
    if (!code || code.length !== 6) return alert('لطفاً کد ۶ رقمی را وارد کنید.');

    const formData = new FormData();
    formData.append('action', 'serene_2fa_verify');
    formData.append('code', code);
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        alert(d.data.message);
        if (d.success) location.reload();
    });
}

function disable2FA() {
    if (!confirm('آیا از غیرفعال‌سازی ورود دو مرحله‌ای اطمینان دارید؟')) return;

    const formData = new FormData();
    formData.append('action', 'serene_2fa_disable');
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        alert(d.data.message);
        if (d.success) location.reload();
    });
}

function registerBiometricDevice() {
    if (!window.PublicKeyCredential) {
        return alert('دستگاه یا مرورگر شما از قابلیت احراز هویت بیومتریک (WebAuthn/Passkeys) پشتیبانی نمی‌کند.');
    }

    const formData = new FormData();
    formData.append('action', 'serene_webauthn_register_options');
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return alert(data.data.message);
        const options = data.data;
        
        // Convert challenge & user id to ArrayBuffer
        options.challenge = Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0));
        options.user.id = Uint8Array.from(atob(options.user.id), c => c.charCodeAt(0));

        navigator.credentials.create({ publicKey: options })
        .then(cred => {
            const rawId = btoa(String.fromCharCode(...new Uint8Array(cred.rawId)));
            const saveForm = new FormData();
            saveForm.append('action', 'serene_webauthn_register_save');
            saveForm.append('id', cred.id);
            saveForm.append('publicKey', rawId);
            saveForm.append('nonce', sereneConfig.nonce);

            fetch(sereneConfig.ajax_url, { method: 'POST', body: saveForm })
            .then(r => r.json())
            .then(res => {
                alert(res.data.message);
                if (res.success) location.reload();
            });
        })
        .catch(err => {
            alert('ثبت اثر انگشت با خطا مواجه شد یا توسط کاربر لغو گردید.');
        });
    });
}

function deleteBiometricKey(keyId) {
    if (!confirm('آیا از حذف این کلید بیومتریک اطمینان دارید؟')) return;

    const formData = new FormData();
    formData.append('action', 'serene_webauthn_delete_key');
    formData.append('id', keyId);
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        alert(d.data.message);
        if (d.success) location.reload();
    });
}

function revokeSession(sessionId) {
    if (!confirm('آیا از قطع دسترسی این دستگاه اطمینان دارید؟')) return;

    const formData = new FormData();
    formData.append('action', 'serene_revoke_session');
    formData.append('session_id', sessionId);
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        alert(d.data.message);
        if (d.success) location.reload();
    });
}

function revokeAllSessions() {
    if (!confirm('آیا از خروج از تمامی دستگاه‌های دیگر اطمینان دارید؟')) return;

    const formData = new FormData();
    formData.append('action', 'serene_revoke_all_sessions');
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        alert(d.data.message);
        if (d.success) location.reload();
    });
}
</script>
