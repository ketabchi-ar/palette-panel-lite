<?php
if (!defined('ABSPATH')) exit;
$can_spin = Serene_Panel_Lucky_Wheel::can_user_spin($user_id);
$slices   = Serene_Panel_Lucky_Wheel::get_wheel_slices();
?>
<div class="serene-app space-y-6 text-center max-w-xl mx-auto py-4" dir="rtl">
    <div>
        <span class="text-xs font-bold text-tertiary bg-tertiary-container px-3 py-1 rounded-full">گیمیفیکیشن و شانس روزانه</span>
        <h1 class="text-2xl md:text-3xl font-black text-on-surface mt-3">گردونه شانس مشتریان 🎡</h1>
        <p class="text-xs md:text-sm text-on-surface-variant mt-1.5 leading-relaxed">هر روز یک بار گردونه را بچرخانید و جوایز نقدی، شارژ کیف پول و کدهای تخفیف شگفت‌انگیز دریافت کنید!</p>
    </div>

    <!-- Wheel Container -->
    <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl border border-outline-variant/40 shadow-sm flex flex-col items-center justify-center space-y-6 relative overflow-hidden">
        <div class="relative w-[300px] h-[300px] md:w-[360px] md:h-[360px] flex items-center justify-center" style="max-width:100%;">
            <!-- Pointer Indicator -->
            <div class="absolute top-0 z-30 text-rose-600 transform -translate-y-3 drop-shadow-md">
                <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">arrow_drop_down</span>
            </div>
            
            <canvas id="luckyWheelCanvas" width="360" height="360" class="rounded-full shadow-2xl" style="transition: transform 4.5s cubic-bezier(0.15, 0.9, 0.25, 1);"></canvas>
            
            <!-- Center Spin Button -->
            <button id="spin-btn" onclick="spinLuckyWheel()" class="absolute z-30 w-16 h-16 md:w-20 md:h-20 rounded-full bg-white text-primary font-black text-xs md:text-sm shadow-2xl ring-4 ring-primary-container flex flex-col items-center justify-center hover:scale-105 active:scale-95 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-lg mb-0.5">casino</span>
                <span>بچرخون</span>
            </button>
        </div>

        <div id="wheel-result-msg" class="hidden w-full p-4 rounded-2xl text-sm font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 animate-fade-in text-center"></div>

        <?php if (!$can_spin): ?>
            <div id="spin-limit-warn" class="text-xs text-amber-700 bg-amber-50 border border-amber-200 px-4 py-2.5 rounded-xl font-medium">
                ⚠️ شما سهمیه چرخش امروز خود را استفاده کرده‌اید. فردا دوباره امتحان کنید!
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    let wheelSlices = <?php echo wp_json_encode($slices); ?>;
    if (!wheelSlices || !wheelSlices.length) {
        wheelSlices = (window.sereneConfig && window.sereneConfig.lucky_wheel_slices) ? window.sereneConfig.lucky_wheel_slices : [];
    }

    function initAndDrawWheel() {
        const canvas = document.getElementById('luckyWheelCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const totalSlices = wheelSlices.length;
        if (totalSlices === 0) return;

        const arc = (2 * Math.PI) / totalSlices;
        const radius = canvas.width / 2;

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        wheelSlices.forEach((slice, i) => {
            const angle = i * arc;
            ctx.beginPath();
            ctx.fillStyle = slice.color || '#4c5e8b';
            ctx.moveTo(radius, radius);
            ctx.arc(radius, radius, radius, angle, angle + arc);
            ctx.lineTo(radius, radius);
            ctx.fill();

            // Border
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.5)';
            ctx.lineWidth = 2;
            ctx.stroke();

            // Text
            ctx.save();
            ctx.fillStyle = slice.text || '#ffffff';
            ctx.translate(radius, radius);
            ctx.rotate(angle + arc / 2);
            ctx.textAlign = 'right';
            ctx.font = 'bold 13px Vazirmatn, Tahoma, sans-serif';
            ctx.fillText(slice.label, radius - 24, 5);
            ctx.restore();
        });
    }

    let currentDeg = 0;
    window.spinLuckyWheel = function() {
        const btn = document.getElementById('spin-btn');
        if (btn) btn.disabled = true;

        const formData = new FormData();
        formData.append('action', 'serene_spin_wheel');
        formData.append('nonce', sereneConfig.nonce);

        fetch(sereneConfig.ajax_url, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(d => {
            if (!d.success) {
                alert(d.data.message || 'خطا در چرخش گردونه.');
                if (btn) btn.disabled = false;
                return;
            }

            const winningIndex = d.data.slice_index !== undefined ? d.data.slice_index : 0;
            const totalSlices = wheelSlices.length;
            const sliceDeg = 360 / totalSlices;
            
            const targetDeg = (360 - (winningIndex * sliceDeg)) - (sliceDeg / 2) - 90;
            const extraRotations = 1800;
            const finalRotation = currentDeg + extraRotations + ((targetDeg - (currentDeg % 360) + 360) % 360);

            currentDeg = finalRotation;
            const canvas = document.getElementById('luckyWheelCanvas');
            if (canvas) {
                canvas.style.transform = 'rotate(' + finalRotation + 'deg)';
            }

            setTimeout(() => {
                const resEl = document.getElementById('wheel-result-msg');
                if (resEl) {
                    const prizeLabel = d.data.label || (d.data.prize && d.data.prize.label) || 'جایزه ویژه';
                    const prizeType = d.data.prize ? d.data.prize.type : '';
                    
                    if (prizeType === 'empty') {
                        resEl.className = 'w-full p-4 rounded-2xl text-xs md:text-sm font-bold bg-slate-100 text-slate-700 border border-slate-300 animate-fade-in text-center';
                        resEl.innerHTML = '😔 <strong>متاسفانه این بار برنده نشدید!</strong><br><span class="text-xs text-on-surface-variant font-normal">فردا مجدداً شانس خود را امتحان کنید.</span>';
                    } else {
                        resEl.className = 'w-full p-5 rounded-2xl text-xs md:text-sm font-bold bg-emerald-50 text-emerald-900 border border-emerald-300 shadow-sm animate-fade-in text-center space-y-3';
                        let msg = '🎉 <strong class="text-base">تبریک!</strong> شما برنده شدید: <br><span class="text-xl md:text-2xl font-black text-primary inline-block my-1">' + prizeLabel + '</span>';
                        
                        if (d.data.coupon_code) {
                            msg += '<div class="p-3 bg-white rounded-xl border border-dashed border-primary flex items-center justify-between gap-3 max-w-xs mx-auto shadow-inner" dir="ltr">' +
                                   '<span class="font-mono font-black text-base tracking-wider text-primary select-all">' + d.data.coupon_code + '</span>' +
                                   '<button type="button" onclick="navigator.clipboard.writeText(\'' + d.data.coupon_code + '\'); alert(\'کد تخفیف کپی شد!\')" class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-primary-dim transition-all cursor-pointer">کپی کد</button>' +
                                   '</div>';
                            msg += '<div class="text-[11px] text-emerald-800 font-normal">مهلت استفاده: <strong>' + (d.data.expiry_days || 7) + ' روز</strong> • در بخش «جوایز من» نیز ثبت شد.</div>';
                        } else if (prizeType === 'wallet') {
                            msg += '<div class="text-xs text-emerald-800 font-normal">مبلغ جایزه بلافاصله به کیف پول شما واریز گردید!</div>';
                        }
                        
                        msg += '<div class="pt-2"><a href="?tab=rewards" class="inline-flex items-center gap-1 text-xs text-primary font-bold hover:underline"><span class="material-symbols-outlined text-sm">card_giftcard</span><span>مشاهده در بخش جوایز من</span></a></div>';
                        resEl.innerHTML = msg;
                    }
                    resEl.classList.remove('hidden');
                }
                if (btn) btn.disabled = false;
            }, 4600);
        })
        .catch(err => {
            alert('خطای ارتباط با سرور.');
            if (btn) btn.disabled = false;
        });
    };

    initAndDrawWheel();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAndDrawWheel);
    }
    setTimeout(initAndDrawWheel, 100);
    setTimeout(initAndDrawWheel, 500);
})();
</script>