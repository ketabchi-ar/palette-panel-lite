<?php
if (!defined('ABSPATH')) exit;
$options = get_option('serene_panel_options', []);
$enable_google   = !empty($options['enable_google_auth']) && !empty($options['google_client_id']);
$enable_bale     = !empty($options['enable_bale_auth']);
$enable_telegram = !empty($options['enable_telegram_auth']);
$enable_voice    = !empty($options['enable_voice_auth']);
$enable_biometric= !empty($options['enable_biometric']);
$google_client_id= $options['google_client_id'] ?? '';
$login_terms_url = $options['login_terms_url'] ?? '#';
$show_admin_link = !empty($options['login_show_admin_link']);
?>

<div class="auth-form-wrapper space-y-6">
    <!-- Top Horizontal Mode Switcher (OTP vs Password) -->
    <div class="flex p-1.5 bg-surface-container-low rounded-2xl gap-1">
        <button type="button" onclick="switchAuthTab('tab-mode-otp', this)" id="btn-tab-otp" class="auth-mode-tab active flex-1 py-2.5 px-3 rounded-xl text-xs font-bold bg-white text-primary shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <span class="material-symbols-outlined text-base">sms</span>
            <span>ورود با شماره موبایل (OTP)</span>
        </button>
        <button type="button" onclick="switchAuthTab('tab-mode-password', this)" id="btn-tab-password" class="auth-mode-tab flex-1 py-2.5 px-3 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <span class="material-symbols-outlined text-base">lock</span>
            <span>ورود با رمز عبور</span>
        </button>
    </div>

    <!-- MODE 1: OTP Flow -->
    <div id="tab-mode-otp" class="auth-tab-content space-y-6">
        <!-- STEP 1: Phone Input -->
        <div id="auth-step-phone" class="space-y-5">
            <div>
                <h3 class="text-base font-black text-on-surface">ورود یا ثبت‌نام سریع</h3>
                <p class="text-xs text-on-surface-variant mt-1">شماره همراه خود را جهت دریافت کد تایید یکبارمصرف وارد نمایید.</p>
            </div>

            <div class="space-y-4">
                <label class="block">
                    <span class="block text-xs font-bold text-on-surface-variant mb-2">شماره همراه</span>
                    <div class="relative flex items-center">
                        <div class="absolute right-4 flex items-center gap-2 text-on-surface-variant">
                            <span class="material-symbols-outlined text-lg">phone_iphone</span>
                            <span class="text-sm border-l border-outline-variant/50 pl-2 ml-1 font-mono">+98</span>
                        </div>
                        <input id="auth-phone" type="tel" placeholder="9123456789" dir="ltr" class="w-full bg-surface-container-low border-none focus:ring-2 focus:ring-primary rounded-2xl py-3.5 pr-24 pl-4 text-sm font-mono font-bold transition-all">
                    </div>
                </label>

                <button type="button" onclick="sendOTP('sms', 'text')" id="btn-send-otp" class="w-full bg-primary hover:bg-primary-dim text-white font-bold text-sm py-4 rounded-2xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span>ارسال کد تایید پیامکی</span>
                    <span class="material-symbols-outlined transform rotate-180 text-lg">arrow_forward</span>
                </button>

                <!-- Alternative Auth Options -->
                <?php if ($enable_voice || $enable_bale || $enable_telegram || $enable_biometric): ?>
                <div class="pt-4 border-t border-outline-variant/20 space-y-2.5">
                    <div class="text-[11px] text-center text-on-surface-variant font-bold">سایر روش‌های ارسال کد و ورود:</div>
                    <div class="grid grid-cols-2 gap-2">
                        <?php if ($enable_biometric): ?>
                        <button type="button" onclick="loginWithBiometrics()" class="w-full bg-surface-container hover:bg-surface-container-high text-on-surface py-2.5 px-2 rounded-xl text-[11px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-base text-primary">fingerprint</span>
                            <span>اثر انگشت</span>
                        </button>
                        <?php endif; ?>

                        <?php if ($enable_voice): ?>
                        <button type="button" onclick="sendOTP('sms', 'voice')" class="w-full bg-surface-container hover:bg-surface-container-high text-on-surface py-2.5 px-2 rounded-xl text-[11px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-base text-purple-600">phone_in_talk</span>
                            <span>تماس صوتی</span>
                        </button>
                        <?php endif; ?>

                        <?php if ($enable_bale): ?>
                        <button type="button" onclick="sendOTP('bale', 'text')" class="w-full bg-surface-container hover:bg-surface-container-high text-on-surface py-2.5 px-2 rounded-xl text-[11px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-base text-emerald-600">chat</span>
                            <span>ارسال به بله</span>
                        </button>
                        <?php endif; ?>

                        <?php if ($enable_telegram): ?>
                        <button type="button" onclick="sendOTP('telegram', 'text')" class="w-full bg-surface-container hover:bg-surface-container-high text-on-surface py-2.5 px-2 rounded-xl text-[11px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-base text-sky-600">send</span>
                            <span>ارسال به تلگرام</span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Google OAuth Button -->
                <?php if ($enable_google): ?>
                <div class="pt-2">
                    <div id="g_id_onload"
                         data-client_id="<?php echo esc_attr($google_client_id); ?>"
                         data-callback="handleGoogleLogin"
                         data-auto_prompt="false">
                    </div>
                    <div class="g_id_signin w-full flex justify-center" data-type="standard" data-size="large" data-theme="outline" data-text="sign_in_with" data-shape="rectangular" data-logo_alignment="left"></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- STEP 2: OTP Code Verification -->
        <div id="auth-step-otp" class="hidden space-y-6">
            <div>
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-on-surface">تایید کد یکبارمصرف</h3>
                    <button type="button" onclick="backToPhoneStep()" class="text-xs text-primary font-bold flex items-center gap-1 cursor-pointer">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        <span>ویرایش شماره</span>
                    </button>
                </div>
                <p class="text-xs text-on-surface-variant mt-1">کد تایید ارسال شده به <strong id="display-phone" class="font-mono text-primary"></strong> را وارد کنید.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <input id="auth-otp" type="text" maxlength="6" placeholder="• • • • •" dir="ltr" class="w-full bg-surface-container-low border-none focus:ring-2 focus:ring-primary rounded-2xl py-4 text-center font-mono text-xl tracking-[0.6em] font-black transition-all">
                </div>

                <div class="flex items-center justify-between text-xs text-on-surface-variant">
                    <span id="otp-timer-box">ارسال مجدد پس از: <strong id="otp-timer" class="font-mono font-bold text-primary">120</strong> ثانیه</span>
                    <button type="button" id="btn-resend-otp" onclick="resendOTP()" class="hidden text-primary font-bold hover:underline cursor-pointer">ارسال مجدد پیامک</button>
                </div>

                <button type="button" onclick="verifyOTP()" id="btn-verify-otp" class="w-full bg-primary hover:bg-primary-dim text-white font-bold text-sm py-4 rounded-2xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span>تایید و ورود به حساب</span>
                    <span class="material-symbols-outlined text-lg">check</span>
                </button>
            </div>
        </div>

        <!-- STEP 3: 2FA TOTP Code (Optional) -->
        <div id="auth-step-2fa" class="hidden space-y-6">
            <div>
                <h3 class="text-base font-bold text-on-surface">کد تایید دو مرحله‌ای (2FA)</h3>
                <p class="text-xs text-on-surface-variant mt-1">کد ۶ رقمی اپلیکیشن Google Authenticator را وارد کنید.</p>
            </div>

            <div class="space-y-4">
                <input id="auth-2fa" type="text" maxlength="6" placeholder="• • • • • •" dir="ltr" class="w-full bg-surface-container-low border-none focus:ring-2 focus:ring-primary rounded-2xl py-4 text-center font-mono text-xl tracking-[0.5em] font-black">
                <button type="button" onclick="verify2FA()" id="btn-verify-2fa" class="w-full bg-primary hover:bg-primary-dim text-white font-bold text-sm py-4 rounded-2xl shadow-md cursor-pointer">
                    تایید ورود دو مرحله‌ای
                </button>
            </div>
        </div>
    </div>

    <!-- MODE 2: Standard Username & Password Flow -->
    <div id="tab-mode-password" class="auth-tab-content hidden space-y-5">
        <div>
            <h3 class="text-base font-black text-on-surface">ورود با نام کاربری و رمز عبور</h3>
            <p class="text-xs text-on-surface-variant mt-1">ویژه مدیران سایت و کاربرانی که دارای کلمه عبور هستند.</p>
        </div>

        <form id="form-login-password" onsubmit="handlePasswordLogin(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-2">نام کاربری، ایمیل یا شماره موبایل</label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute right-3 text-on-surface-variant text-base">person</span>
                    <input type="text" id="pwd-user-login" placeholder="نام کاربری یا ایمیل..." class="w-full bg-surface-container-low border-none focus:ring-2 focus:ring-primary rounded-2xl py-3.5 pr-10 pl-4 text-xs font-medium transition-all" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-2">کلمه عبور</label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute right-3 text-on-surface-variant text-base">key</span>
                    <input type="password" id="pwd-user-password" placeholder="••••••••" class="w-full bg-surface-container-low border-none focus:ring-2 focus:ring-primary rounded-2xl py-3.5 pr-10 pl-10 text-xs font-mono transition-all" required>
                    <button type="button" onclick="togglePasswordVisibility('pwd-user-password', this)" class="absolute left-3 text-on-surface-variant hover:text-primary cursor-pointer">
                        <span class="material-symbols-outlined text-base">visibility</span>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="pwd-remember-me" checked class="rounded text-primary w-4 h-4">
                    <span class="text-on-surface-variant font-bold">مرا به خاطر بسپار</span>
                </label>
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" target="_blank" class="text-primary hover:underline font-bold">فراموشی رمز عبور؟</a>
            </div>

            <button type="submit" id="btn-pwd-login" class="w-full bg-primary hover:bg-primary-dim text-white font-bold text-sm py-4 rounded-2xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>ورود به حساب کاربری</span>
                <span class="material-symbols-outlined text-lg">login</span>
            </button>
        </form>
    </div>

    <!-- Terms & Footer -->
    <div class="text-[11px] text-center text-on-surface-variant pt-2 border-t border-outline-variant/10 space-y-2">
        <p>با ورود به سامانه، <a href="<?php echo esc_url($login_terms_url); ?>" target="_blank" class="text-primary underline font-bold">قوانین و مقررات حریم خصوصی</a> را می‌پذیرید.</p>
        <?php if ($show_admin_link): ?>
            <div>
                <a href="<?php echo esc_url(wp_login_url()); ?>?admin_standard=1" class="text-[10px] text-slate-400 hover:text-primary">ورود مستقیم به پیشخوان مدیریت وردپرس</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function switchAuthTab(tabId, btn) {
    document.querySelectorAll('.auth-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.auth-mode-tab').forEach(b => {
        b.className = 'auth-mode-tab flex-1 py-2.5 px-3 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all flex items-center justify-center gap-1.5 cursor-pointer';
    });

    const activeEl = document.getElementById(tabId);
    if (activeEl) activeEl.classList.remove('hidden');

    btn.className = 'auth-mode-tab active flex-1 py-2.5 px-3 rounded-xl text-xs font-bold bg-white text-primary shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer';
}

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('.material-symbols-outlined');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) icon.innerText = 'visibility_off';
    } else {
        input.type = 'password';
        if (icon) icon.innerText = 'visibility';
    }
}

function handlePasswordLogin(e) {
    e.preventDefault();
    const login = document.getElementById('pwd-user-login').value.trim();
    const pass = document.getElementById('pwd-user-password').value;
    const remember = document.getElementById('pwd-remember-me').checked;
    const btn = document.getElementById('btn-pwd-login');

    if (!login || !pass) {
        alert('لطفاً نام کاربری و رمز عبور را وارد نمایید.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-lg">sync</span><span>در حال بررسی مشخصات...</span>';

    fetch(window.sereneConfig ? window.sereneConfig.ajax_url : '<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'palette_login_with_password',
            log: login,
            pwd: pass,
            rememberme: remember ? 'forever' : '',
            nonce: window.sereneConfig ? window.sereneConfig.nonce : '<?php echo wp_create_nonce('serene_panel_nonce'); ?>'
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            btn.innerHTML = '<span class="material-symbols-outlined text-lg">check_circle</span><span>ورود موفقیت‌آمیز! در حال انتقال...</span>';
            btn.className = 'w-full bg-emerald-600 text-white font-bold text-sm py-4 rounded-2xl flex items-center justify-center gap-2';
            setTimeout(() => {
                window.location.href = d.data.redirect_url || '<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>';
            }, 1000);
        } else {
            btn.disabled = false;
            btn.innerHTML = '<span>ورود به حساب کاربری</span><span class="material-symbols-outlined text-lg">login</span>';
            alert(d.data.message || 'نام کاربری یا رمز عبور اشتباه است.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<span>ورود به حساب کاربری</span><span class="material-symbols-outlined text-lg">login</span>';
        alert('خطای ارتباط با سرور.');
    });
}
</script>
