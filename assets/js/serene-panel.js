/**
 * Palette Panel - Core Frontend Script
 * Author: Palette Digital Agency (https://palette.agency/)
 */

let countdownInterval = null;
let currentPendingUserId = null;
let currentPhone = '';
let currentAuthType = 'sms';
let currentAuthMethod = 'text';

function sendOTP(type = 'sms', method = 'text') {
    const phoneInput = document.getElementById('auth-phone');
    const phone = phoneInput ? phoneInput.value.trim() : '';
    if (!phone) {
        alert('لطفاً شماره موبایل خود را وارد کنید.');
        return;
    }

    currentPhone = phone;
    currentAuthType = type;
    currentAuthMethod = method;

    const btn = document.getElementById('btn-send-otp');
    const oldBtnText = btn ? btn.innerHTML : '';
    if (btn) btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-lg">sync</span><span>در حال ارسال...</span>';

    const formData = new FormData();
    formData.append('action', 'serene_send_otp');
    formData.append('phone', phone);
    formData.append('type', type);
    formData.append('method', method);
    formData.append('nonce', window.sereneConfig ? window.sereneConfig.nonce : '');

    fetch(window.sereneConfig.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (btn) btn.innerHTML = oldBtnText;
        if (data.success) {
            document.getElementById('auth-step-phone').classList.add('hidden');
            document.getElementById('auth-step-otp').classList.remove('hidden');
            document.getElementById('display-phone').innerText = phone;
            startCountdownTimer();
            setupOTPInputs();
        } else {
            alert(data.data.message || 'خطا در ارسال کد تایید.');
        }
    })
    .catch(() => {
        if (btn) btn.innerHTML = oldBtnText;
        alert('خطای اتصال به سرور.');
    });
}

function setupOTPInputs() {
    const inputs = document.querySelectorAll('#otp-inputs-wrapper input');
    if (!inputs.length) return;

    inputs.forEach((input, index) => {
        input.value = '';
        input.oninput = (e) => {
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            if (index === inputs.length - 1 && input.value.length === 1) {
                verifyOTP();
            }
        };
        input.onkeydown = (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        };
        input.onpaste = (e) => {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
            if (pasteData) {
                const chars = pasteData.split('');
                inputs.forEach((inp, i) => {
                    if (chars[i]) inp.value = chars[i];
                });
                if (chars.length >= inputs.length) {
                    verifyOTP();
                }
            }
        };
    });

    setTimeout(() => { inputs[0].focus(); }, 150);
}

function verifyOTP() {
    const inputs = document.querySelectorAll('#otp-inputs-wrapper input');
    let code = '';
    inputs.forEach(i => code += i.value);

    if (code.length < 4) {
        alert('لطفاً کد تایید را به طور کامل وارد کنید.');
        return;
    }

    const btn = document.getElementById('btn-verify-otp');
    if (btn) btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-lg">sync</span><span>در حال بررسی...</span>';

    const formData = new FormData();
    formData.append('action', 'serene_verify_otp');
    formData.append('phone', currentPhone);
    formData.append('code', code);
    formData.append('nonce', window.sereneConfig.nonce);

    fetch(window.sereneConfig.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (btn) btn.innerHTML = '<span>تایید و ورود به حساب</span>';
        if (data.success) {
            if (data.data.require_2fa) {
                currentPendingUserId = data.data.user_id;
                document.getElementById('auth-step-otp').classList.add('hidden');
                document.getElementById('auth-step-2fa').classList.remove('hidden');
                document.getElementById('totp-code-input').focus();
            } else {
                window.location.href = data.data.redirect || window.sereneConfig.panel_url;
            }
        } else {
            alert(data.data.message || 'کد وارد شده اشتباه یا منقضی شده است.');
        }
    })
    .catch(() => {
        if (btn) btn.innerHTML = '<span>تایید و ورود به حساب</span>';
        alert('خطای اتصال به سرور.');
    });
}

function verify2FA() {
    const code = document.getElementById('totp-code-input').value.trim();
    if (!code || code.length !== 6) {
        alert('لطفاً کد ۶ رقمی Google Authenticator را وارد کنید.');
        return;
    }

    const btn = document.getElementById('btn-verify-2fa');
    if (btn) btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-lg">sync</span><span>در حال بررسی...</span>';

    const formData = new FormData();
    formData.append('action', 'serene_2fa_verify');
    formData.append('user_id', currentPendingUserId);
    formData.append('code', code);
    formData.append('nonce', window.sereneConfig.nonce);

    fetch(window.sereneConfig.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (btn) btn.innerHTML = '<span>تایید نهایی و ورود به حساب</span>';
        if (data.success) {
            window.location.href = data.data.redirect || window.sereneConfig.panel_url;
        } else {
            alert(data.data.message || 'کد دو مرحله‌ای نامعتبر است.');
        }
    })
    .catch(() => {
        if (btn) btn.innerHTML = '<span>تایید نهایی و ورود به حساب</span>';
        alert('خطا در اعتبارسنجی.');
    });
}

function handleGoogleSignIn(response) {
    if (!response.credential) return;

    const formData = new FormData();
    formData.append('action', 'serene_google_login');
    formData.append('id_token', response.credential);
    formData.append('nonce', window.sereneConfig.nonce);

    fetch(window.sereneConfig.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.data.redirect || window.sereneConfig.panel_url;
        } else {
            alert(data.data.message || 'ورود با گوگل با خطا مواجه شد.');
        }
    })
    .catch(() => {
        alert('خطای ارتباط با سرور.');
    });
}

function loginWithBiometrics() {
    if (!window.PublicKeyCredential) {
        alert('دستگاه شما از ورود بیومتریک (Touch ID / Face ID) پشتیبانی نمی‌کند.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'serene_webauthn_login_options');
    formData.append('nonce', window.sereneConfig.nonce);

    fetch(window.sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return alert(data.data.message);
        const options = data.data;
        options.challenge = Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0));

        navigator.credentials.get({ publicKey: options })
        .then(assertion => {
            const verifyForm = new FormData();
            verifyForm.append('action', 'serene_webauthn_login_verify');
            verifyForm.append('id', assertion.id);
            verifyForm.append('nonce', window.sereneConfig.nonce);

            fetch(window.sereneConfig.ajax_url, { method: 'POST', body: verifyForm })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    window.location.href = res.data.redirect || window.sereneConfig.panel_url;
                } else {
                    alert(res.data.message);
                }
            });
        })
        .catch(err => {
            alert('ورود با اثر انگشت لغو شد یا در دستگاه ثبت نشده است.');
        });
    });
}

function startCountdownTimer(seconds = 120) {
    clearInterval(countdownInterval);
    const timerDisplay = document.getElementById('countdown-timer');
    const resendBtn = document.getElementById('btn-resend');
    if (!timerDisplay || !resendBtn) return;

    resendBtn.disabled = true;
    let remaining = seconds;

    countdownInterval = setInterval(() => {
        const mins = Math.floor(remaining / 60);
        const secs = remaining % 60;
        timerDisplay.innerText = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;

        if (remaining <= 0) {
            clearInterval(countdownInterval);
            resendBtn.disabled = false;
            timerDisplay.innerText = '00:00';
        }
        remaining--;
    }, 1000);
}

function resendOTP() {
    sendOTP(currentAuthType, currentAuthMethod);
}

function backToPhoneStep() {
    clearInterval(countdownInterval);
    document.getElementById('auth-step-otp').classList.add('hidden');
    document.getElementById('auth-step-2fa').classList.add('hidden');
    document.getElementById('auth-step-phone').classList.remove('hidden');
}

// Global Click outside dropdown listener
document.addEventListener('click', function(e) {
    const notifDropdown = document.getElementById('notif-dropdown');
    if (notifDropdown && !e.target.closest('#notif-dropdown, button[onclick="toggleNotifs()"]')) {
        notifDropdown.classList.add('hidden');
    }
});
