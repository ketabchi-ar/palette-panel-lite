
/**
 * Serene Auth & OTP Frontend Engine
 */
let otpTimer = null;
let timeLeft = 120;

function sendOTP(method) {
    method = method || 'text';
    const phoneInput = document.getElementById('auth-phone');
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const btn = document.getElementById('btn-send-otp');

    if (!phone) {
        alert('لطفاً شماره موبایل خود را وارد کنید.');
        return;
    }

    if (btn) btn.disabled = true;

    const formData = new FormData();
    formData.append('action', 'serene_send_otp');
    formData.append('phone', phone);
    formData.append('type', 'sms');
    formData.append('method', method);
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (btn) btn.disabled = false;
        if (data.success) {
            document.getElementById('auth-step-phone').classList.add('hidden');
            document.getElementById('auth-step-otp').classList.remove('hidden');
            document.getElementById('display-phone').innerText = phone;
            startCountdown();
            setupOTPInputs();
        } else {
            alert(data.data.message || 'خطا در ارسال کد تایید.');
        }
    })
    .catch(err => {
        if (btn) btn.disabled = false;
        alert('خطای ارتباط با سرور.');
    });
}

function startCountdown() {
    timeLeft = 120;
    const timerEl = document.getElementById('countdown-timer');
    const resendBtn = document.getElementById('btn-resend');
    if (resendBtn) resendBtn.disabled = true;

    clearInterval(otpTimer);
    otpTimer = setInterval(() => {
        timeLeft--;
        const mins = String(Math.floor(timeLeft / 60)).padStart(2, '0');
        const secs = String(timeLeft % 60).padStart(2, '0');
        if (timerEl) timerEl.innerText = mins + ':' + secs;

        if (timeLeft <= 0) {
            clearInterval(otpTimer);
            if (resendBtn) resendBtn.disabled = false;
        }
    }, 1000);
}

function resendOTP() {
    sendOTP();
}

function backToPhoneStep() {
    clearInterval(otpTimer);
    document.getElementById('auth-step-otp').classList.add('hidden');
    document.getElementById('auth-step-phone').classList.remove('hidden');
}

function setupOTPInputs() {
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach((input, index) => {
        input.value = '';
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                } else {
                    verifyOTP();
                }
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    if (inputs[0]) inputs[0].focus();
}

function verifyOTP() {
    const inputs = document.querySelectorAll('.otp-input');
    let code = '';
    inputs.forEach(inp => code += inp.value);

    const phone = document.getElementById('display-phone').innerText;

    if (code.length < 5) {
        alert('لطفاً کد تایید ۵ رقمی را کامل وارد کنید.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'serene_verify_otp');
    formData.append('phone', phone);
    formData.append('code', code);
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.data.require_2fa) {
                const twoFaCode = prompt(data.data.message);
                if (twoFaCode) {
                    verify2FA(data.data.user_id, twoFaCode);
                }
                return;
            }
            window.location.href = data.data.redirect || sereneConfig.panel_url;
        } else {
            alert(data.data.message || 'کد وارد شده اشتباه است.');
        }
    })
    .catch(() => alert('خطا در اعتبارسنجی کد.'));
}

function verify2FA(userId, code) {
    const formData = new FormData();
    formData.append('action', 'serene_2fa_verify');
    formData.append('user_id', userId);
    formData.append('code', code);
    formData.append('nonce', sereneConfig.nonce);

    fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
        if (d.success) window.location.href = d.data.redirect;
        else alert(d.data.message);
    });
}

// WebAuthn Biometric Login
async function loginWithBiometrics() {
    if (!window.PublicKeyCredential) {
        alert('مرورگر شما از ورود بیومتریک (WebAuthn / Passkeys) پشتیبانی نمی‌کند.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'serene_webauthn_login_options');
    formData.append('nonce', sereneConfig.nonce);

    try {
        const res = await fetch(sereneConfig.ajax_url, { method: 'POST', body: formData });
        const data = await res.json();
        
        if (!data.success) {
            alert(data.data.message || 'خطا در احراز هویت بیومتریک.');
            return;
        }

        const challenge = Uint8Array.from(atob(btoa(data.data.challenge)), c => c.charCodeAt(0));
        const assertion = await navigator.credentials.get({
            publicKey: {
                challenge: challenge,
                timeout: 60000,
                userVerification: 'preferred'
            }
        });

        if (assertion) {
            const verifyForm = new FormData();
            verifyForm.append('action', 'serene_webauthn_login_verify');
            verifyForm.append('id', assertion.id);
            verifyForm.append('nonce', sereneConfig.nonce);

            const vRes = await fetch(sereneConfig.ajax_url, { method: 'POST', body: verifyForm });
            const vData = await vRes.json();
            if (vData.success) {
                window.location.href = vData.data.redirect || sereneConfig.panel_url;
            } else {
                alert(vData.data.message);
            }
        }
    } catch (e) {
        console.error(e);
        alert('ورود با اثر انگشت لغو شد یا کلید ثبت‌نشده است.');
    }
}
