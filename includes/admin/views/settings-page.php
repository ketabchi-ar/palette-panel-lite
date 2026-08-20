<?php
if (!defined('ABSPATH')) exit;
$opt = get_option('serene_panel_options', []);
$theme_colors = Serene_Panel_Assets::get_theme_colors();
$menu_items = Serene_Panel_Menu_Builder::get_menu_items();
$custom_fields = Serene_Panel_Form_Builder::get_custom_fields();
$wheel_slices = Serene_Panel_Lucky_Wheel::get_wheel_slices();
$loyalty_tiers = Palette_Panel_Loyalty_Tiers::get_tiers();
$is_woo_wallet = Serene_Panel_Wallet::is_woo_wallet_active();
$carriers = Serene_Panel_Post_Tracker::get_carriers();
$ticket_departments = Serene_Panel_Tickets::get_departments();
?>
<div class="wrap font-body text-right" dir="rtl" id="serene-admin-app">
    <!-- In-Page Floating Notification Toast -->
    <div id="admin-notif-toast" class="hidden fixed bottom-24 left-6 z-50 p-4 rounded-2xl shadow-2xl text-xs font-bold transition-all duration-300 max-w-sm flex items-center gap-3"></div>

    <div class="max-w-7xl mx-auto py-6 space-y-6 pb-28">
        <!-- Header Banner (Soft UI) -->
        <div class="bg-gradient-to-l from-primary via-primary-dim to-slate-900 rounded-[2rem] p-8 text-white shadow-2xl flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 space-y-2">
                <div class="inline-flex items-center gap-2 bg-white/20 px-3.5 py-1.5 rounded-full text-xs font-bold backdrop-blur-md">
                    <span>نسخه ۱.۴.۶</span>
                    <span>•</span>
                    <span>محصول اختصاصی <a href="https://palette.agency/" target="_blank" class="underline hover:text-primary-container">آژانس دیجیتال پالت</a></span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black">پالت پنل (Palette Panel) • مدیریت و پیکربندی جامع</h1>
                <p class="text-white/80 text-xs md:text-sm leading-relaxed">احراز هویت هوشمند چندکاناله، درگاه کارت‌به‌کارت بومی، باشگاه مشتریان پویا، شخصی‌ساز ۴ حالته لاگین، منوساز زنده و ۱۰ ماژول پیشرفته</p>
            </div>
            <div class="relative z-10 flex items-center gap-3">
                <button type="button" onclick="saveSereneSettings()" class="btn-save-trigger bg-white text-primary hover:bg-primary-container px-7 py-3.5 rounded-2xl font-bold text-xs shadow-xl transition-all flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-lg">save</span>
                    <span>ذخیره تنظیمات</span>
                </button>
            </div>
        </div>

        <!-- LAYER 1: Main Category Tabs (Soft Pill Design) -->
        <div class="flex flex-wrap gap-2.5 p-2 bg-surface-container-low rounded-3xl" id="main-tabs-nav">
            <button type="button" onclick="switchMainCat('cat-auth', this)" class="main-cat-tab active px-5 py-3 rounded-2xl font-black text-xs flex items-center gap-2 bg-primary text-white shadow-md transition-all cursor-pointer">
                <span class="material-symbols-outlined text-lg">sms</span>
                <span>۱. پیامک و احراز هویت</span>
            </button>
            <button type="button" onclick="switchMainCat('cat-finance', this)" class="main-cat-tab px-5 py-3 rounded-2xl font-black text-xs flex items-center gap-2 bg-transparent text-on-surface-variant hover:bg-surface-container-high transition-all cursor-pointer">
                <span class="material-symbols-outlined text-lg">account_balance_wallet</span>
                <span>۲. مالی، پرداخت و کیف پول</span>
            </button>
            <button type="button" onclick="switchMainCat('cat-design', this)" class="main-cat-tab px-5 py-3 rounded-2xl font-black text-xs flex items-center gap-2 bg-transparent text-on-surface-variant hover:bg-surface-container-high transition-all cursor-pointer">
                <span class="material-symbols-outlined text-lg">palette</span>
                <span>۳. طراحی، لاگین و منوساز</span>
            </button>
            <button type="button" onclick="switchMainCat('cat-engagement', this)" class="main-cat-tab px-5 py-3 rounded-2xl font-black text-xs flex items-center gap-2 bg-transparent text-on-surface-variant hover:bg-surface-container-high transition-all cursor-pointer">
                <span class="material-symbols-outlined text-lg">casino</span>
                <span>۴. تعامل، نقد و بازی</span>
            </button>
            <button type="button" onclick="switchMainCat('cat-advanced', this)" class="main-cat-tab px-5 py-3 rounded-2xl font-black text-xs flex items-center gap-2 bg-transparent text-on-surface-variant hover:bg-surface-container-high transition-all cursor-pointer">
                <span class="material-symbols-outlined text-lg">rocket_launch</span>
                <span>۵. امکانات پیشرفته (۱۰ ماژول)</span>
            </button>
        </div>

        <form id="serene-settings-form">
            <?php wp_nonce_field('serene_panel_nonce', 'serene_nonce'); ?>

            <!-- ========================================== -->
            <!-- CATEGORY 1: SMS & Auth -->
            <!-- ========================================== -->
            <div id="cat-auth" class="main-cat-content space-y-6">
                <!-- Layer 2 Sub-Tabs -->
                <div class="flex flex-wrap gap-2 p-1.5 bg-surface-container-low rounded-2xl w-fit">
                    <button type="button" onclick="switchSubTab('sub-sms-gateways', this)" class="sub-tab active px-4 py-2 rounded-xl text-xs font-bold bg-white text-primary shadow-sm transition-all">
                        سامانه‌های پیامکی و تست ارسال
                    </button>
                    <button type="button" onclick="switchSubTab('sub-auth-methods', this)" class="sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        کانال‌های احراز هویت (گوگل، تلگرام، بله، بیومتریک، 2FA)
                    </button>
                </div>

                <!-- Sub-tab 1.1: SMS Gateways -->
                <div id="sub-sms-gateways" class="sub-content space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">cell_tower</span>
                                    <span>پیکربندی وب‌سرویس و درگاه‌های پیامک خدماتی OTP</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">سامانه پیامک پیش‌فرض سایت خود را انتخاب نموده و مشخصات اتصال API را وارد فرمایید.</p>
                            </div>
                            <div class="w-72">
                                <label class="block text-[11px] font-bold text-on-surface-variant mb-1">سامانه پیامکی فعال:</label>
                                <select name="settings[sms_provider]" id="sms-provider-select" onchange="toggleSmsFields()" class="w-full bg-surface-container-low border-none rounded-2xl p-3 text-xs font-bold">
                                    <option value="smsir" <?php selected($opt['sms_provider'] ?? 'smsir', 'smsir'); ?>>اس‌ام‌اس دات آی‌آر (SMS.ir - نگارش REST v2)</option>
                                    <option value="melipayamak" <?php selected($opt['sms_provider'] ?? '', 'melipayamak'); ?>>ملی پیامک (Melipayamak)</option>
                                    <option value="kavenegar" <?php selected($opt['sms_provider'] ?? '', 'kavenegar'); ?>>کاوه‌نگار (Kavenegar)</option>
                                    <option value="niksms" <?php selected($opt['sms_provider'] ?? '', 'niksms'); ?>>نیک اس‌ام‌اس (NikSMS)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Provider: SMS.ir (REST v2) -->
                        <div id="provider-smsir" class="sms-fields space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">کلید وب‌سرویس SMS.ir (x-api-key)</label>
                                    <input type="password" name="settings[smsir_api_key]" value="<?php echo esc_attr($opt['smsir_api_key'] ?? ''); ?>" placeholder="کلید API دریافتی از بخش کلیدهای وب‌سرویس SMS.ir" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">شناسه قالب پیامک (Template ID)</label>
                                    <input type="text" name="settings[smsir_template_id]" value="<?php echo esc_attr($opt['smsir_template_id'] ?? ''); ?>" placeholder="مثال: 100000" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">نام دقیق متغیر کد در قالب (پیش‌فرض: Code)</label>
                                    <input type="text" name="settings[smsir_param_name]" value="<?php echo esc_attr($opt['smsir_param_name'] ?? 'Code'); ?>" placeholder="Code" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">خط اختصاصی ارسال عمومی (اختیاری)</label>
                                    <input type="text" name="settings[smsir_linenumber]" value="<?php echo esc_attr($opt['smsir_linenumber'] ?? ''); ?>" placeholder="3000..." class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                </div>
                            </div>

                            <!-- Accordion Guide for SMS.ir -->
                            <details class="group bg-blue-50/80 border border-blue-200/80 rounded-2xl p-4 transition-all duration-300">
                                <summary class="flex justify-between items-center cursor-pointer font-bold text-xs text-blue-900 list-none">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary text-base">help</span>
                                        <span>📖 راهنمای گام‌به‌گام فعالسازی و دریافت کلید وب‌سرویس در SMS.ir</span>
                                    </span>
                                    <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
                                </summary>
                                <div class="mt-4 pt-3 border-t border-blue-200/60 text-xs text-blue-950 space-y-2 leading-relaxed">
                                    <p><strong>مرحله ۱:</strong> وارد پنل کاربری خود در <a href="https://app.sms.ir" target="_blank" class="underline font-bold">app.sms.ir</a> شوید.</p>
                                    <p><strong>مرحله ۲:</strong> از منوی سمت راست وارد بخش <strong>«برنامه‌نویسان» > «کلیدهای وب‌سرویس (API Keys)»</strong> شوید و یک کلید جدید ایجاد کنید.</p>
                                    <p><strong>مرحله ۳:</strong> کلید بلند را کپی کرده و در فیلد <code>x-api-key</code> بالا قرار دهید.</p>
                                    <p><strong>مرحله ۴:</strong> به بخش <strong>«ارسال سریع (Verify)» > «قالب‌ها»</strong> رفته و قالب را ایجاد کنید (مثال: <code>کد تایید ورود شما: #Code#</code>) و عدد شناسه قالب را در فیلد <code>Template ID</code> بالا بگذارید.</p>
                                    <p><strong>نکته مهم:</strong> نام متغیر در فیلد بالا باید دقیقاً با نامی که داخل <code>##</code> در قالب تعریف کرده‌اید یکسان باشد (مثلاً <code>Code</code>).</p>
                                </div>
                            </details>
                        </div>

                        <!-- Provider: Melipayamak -->
                        <div id="provider-melipayamak" class="sms-fields hidden space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">نام کاربری ملی پیامک</label>
                                    <input type="text" name="settings[melipayamak_username]" value="<?php echo esc_attr($opt['melipayamak_username'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left" dir="ltr">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">کلمه عبور ملی پیامک</label>
                                    <input type="password" name="settings[melipayamak_password]" value="<?php echo esc_attr($opt['melipayamak_password'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left" dir="ltr">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">کد پترن اعتبارسنجی OTP</label>
                                    <input type="text" name="settings[melipayamak_pattern]" value="<?php echo esc_attr($opt['melipayamak_pattern'] ?? ''); ?>" placeholder="مثال: 123456" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                </div>
                            </div>
                        </div>

                        <!-- Provider: Kavenegar -->
                        <div id="provider-kavenegar" class="sms-fields hidden space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">کلید وب‌سرویس کاوه‌نگار (API Key)</label>
                                    <input type="password" name="settings[kavenegar_api_key]" value="<?php echo esc_attr($opt['kavenegar_api_key'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">نام الگوی اعتبارسنجی (Pattern / Verify)</label>
                                    <input type="text" name="settings[kavenegar_pattern]" value="<?php echo esc_attr($opt['kavenegar_pattern'] ?? 'verify'); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left" dir="ltr">
                                </div>
                            </div>
                        </div>

                        <!-- Provider: NikSMS -->
                        <div id="provider-niksms" class="sms-fields hidden space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">نام کاربری نیک اس‌ام‌اس</label>
                                    <input type="text" name="settings[niksms_username]" value="<?php echo esc_attr($opt['niksms_username'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left" dir="ltr">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">کلمه عبور</label>
                                    <input type="password" name="settings[niksms_password]" value="<?php echo esc_attr($opt['niksms_password'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left" dir="ltr">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">کد پترن OTP</label>
                                    <input type="text" name="settings[niksms_pattern]" value="<?php echo esc_attr($opt['niksms_pattern'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                </div>
                            </div>
                        </div>

                        <!-- Test SMS Tool Box (Soft Card) -->
                        <div class="bg-surface-container-low p-5 rounded-2xl space-y-3">
                            <h4 class="text-xs font-bold text-on-surface flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-primary text-base">send_to_mobile</span>
                                <span>ابزار تست و راستی‌آزمایی ارسال پیامک</span>
                            </h4>
                            <div class="flex flex-col md:flex-row gap-3 items-center">
                                <input type="tel" id="test-sms-phone" placeholder="شماره موبایل جهت تست (مثال: 09123456789)" class="w-full md:w-80 bg-white border-none rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                <button type="button" onclick="sendTestSMS()" class="bg-primary hover:bg-primary-dim text-white px-6 py-3.5 rounded-2xl text-xs font-bold shadow-md transition-all flex items-center gap-1 cursor-pointer whitespace-nowrap">
                                    <span>ارسال پیامک تستی</span>
                                    <span class="material-symbols-outlined text-sm">outgoing_mail</span>
                                </button>
                            </div>
                            <div id="test-sms-result" class="hidden text-xs p-3 rounded-xl"></div>
                        </div>
                    </div>
                </div>

                <!-- Sub-tab 1.2: Multi-Gateway Auth & OTP Scenarios -->
                <div id="sub-auth-methods" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">key</span>
                            <span>روش‌ها و کانال‌های احراز هویت هوشمند کاربران</span>
                        </h3>

                        <!-- Auth Gateways Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Google OAuth -->
                            <div class="p-5 bg-surface-container-low rounded-2xl space-y-3 border border-outline-variant/20 hover:border-primary/40 hover:shadow-sm transition-all">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-on-surface flex items-center gap-2">
                                        <span class="material-symbols-outlined text-rose-600">account_circle</span>
                                        <span>ورود تک‌کلیکه با گوگل (Google OAuth)</span>
                                    </span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[enable_google_auth]" value="1" <?php checked(!empty($opt['enable_google_auth'])); ?> class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <div class="pt-2 border-t border-outline-variant/10">
                                    <input type="text" name="settings[google_client_id]" value="<?php echo esc_attr($opt['google_client_id'] ?? ''); ?>" placeholder="Google Client ID" class="w-full bg-white border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3 text-xs text-left font-mono" dir="ltr">
                                </div>
                            </div>

                            <!-- Bale Bot -->
                            <div class="p-5 bg-surface-container-low rounded-2xl space-y-3 border border-outline-variant/20 hover:border-primary/40 hover:shadow-sm transition-all">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-on-surface flex items-center gap-2">
                                        <span class="material-symbols-outlined text-emerald-600">chat</span>
                                        <span>ارسال کد OTP با پیام‌رسان بله (Bale Bot)</span>
                                    </span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[enable_bale_auth]" value="1" <?php checked(!empty($opt['enable_bale_auth'])); ?> class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <div class="space-y-2 pt-2 border-t border-outline-variant/10">
                                    <input type="password" name="settings[bale_token]" value="<?php echo esc_attr($opt['bale_token'] ?? ''); ?>" placeholder="توکن ربات بله" class="w-full bg-white border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3 text-xs text-left font-mono" dir="ltr">
                                    <input type="text" name="settings[bale_bot_username]" value="<?php echo esc_attr($opt['bale_bot_username'] ?? ''); ?>" placeholder="آیدی ربات بله (بدون @)" class="w-full bg-white border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3 text-xs text-left" dir="ltr">
                                </div>
                            </div>

                            <!-- Telegram Bot -->
                            <div class="p-5 bg-surface-container-low rounded-2xl space-y-3 border border-outline-variant/20 hover:border-primary/40 hover:shadow-sm transition-all">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-on-surface flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sky-600">send</span>
                                        <span>ارسال کد OTP با ربات تلگرام</span>
                                    </span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[enable_telegram_auth]" value="1" <?php checked(!empty($opt['enable_telegram_auth'])); ?> class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <div class="space-y-2 pt-2 border-t border-outline-variant/10">
                                    <input type="password" name="settings[telegram_token]" value="<?php echo esc_attr($opt['telegram_token'] ?? ''); ?>" placeholder="توکن ربات تلگرام" class="w-full bg-white border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3 text-xs text-left font-mono" dir="ltr">
                                    <input type="text" name="settings[telegram_bot_username]" value="<?php echo esc_attr($opt['telegram_bot_username'] ?? ''); ?>" placeholder="آیدی ربات تلگرام" class="w-full bg-white border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3 text-xs text-left" dir="ltr">
                                </div>
                            </div>

                            <!-- Voice Call TTS -->
                            <div class="p-5 bg-surface-container-low rounded-2xl space-y-3 border border-outline-variant/20 hover:border-primary/40 hover:shadow-sm transition-all">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-on-surface flex items-center gap-2">
                                        <span class="material-symbols-outlined text-indigo-600">phone_in_talk</span>
                                        <span>تماس صوتی خواندن کد تایید (Voice Call)</span>
                                    </span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[enable_voice_auth]" value="1" <?php checked(!empty($opt['enable_voice_auth'])); ?> class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <div class="pt-2 border-t border-outline-variant/10">
                                    <input type="text" name="settings[voice_tts_text]" value="<?php echo esc_attr($opt['voice_tts_text'] ?? 'کد تایید ورود شما به سایت %s است.'); ?>" placeholder="متن خوانده شده تماس صوتی" class="w-full bg-white border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3 text-xs">
                                </div>
                            </div>

                            <!-- Biometrics Passkeys -->
                            <div class="p-5 bg-surface-container-low rounded-2xl space-y-3 border border-outline-variant/20 hover:border-primary/40 hover:shadow-sm transition-all">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-on-surface flex items-center gap-2">
                                        <span class="material-symbols-outlined text-teal-600">fingerprint</span>
                                        <span>ورود بیومتریک و اثر انگشت (Passkeys)</span>
                                    </span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[enable_biometric]" value="1" <?php checked(!empty($opt['enable_biometric'])); ?> class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <p class="text-[11px] text-on-surface-variant">ورود بدون نیاز به رمز و پیامک با اثر انگشت یا FaceID گوشی.</p>
                            </div>

                            <!-- Two Factor TOTP -->
                            <div class="p-5 bg-surface-container-low rounded-2xl space-y-3 border border-outline-variant/20 hover:border-primary/40 hover:shadow-sm transition-all">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-on-surface flex items-center gap-2">
                                        <span class="material-symbols-outlined text-purple-600">phonelink_lock</span>
                                        <span>تایید دو مرحله‌ای (Google Authenticator 2FA)</span>
                                    </span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[enable_2fa]" value="1" <?php checked(!empty($opt['enable_2fa'])); ?> class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <p class="text-[11px] text-on-surface-variant">امنیت پیشرفته با کدهای ۶ رقمی اپلیکیشن تایید هویت.</p>
                            </div>
                        </div>

                        <!-- Accordion Guide for Auth Channels -->
                        <details class="group bg-blue-50/80 border border-blue-200/80 rounded-2xl p-4 transition-all duration-300">
                            <summary class="flex justify-between items-center cursor-pointer font-bold text-xs text-blue-900 list-none">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-base">help</span>
                                    <span>📖 راهنمای فعالسازی ورود با گوگل، ربات بله و تلگرام</span>
                                </span>
                                <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="mt-4 pt-3 border-t border-blue-200/60 text-xs text-blue-950 space-y-2 leading-relaxed">
                                <p><strong>ورود با گوگل:</strong> به <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="underline font-bold">Google Cloud Console</a> بروید، یک OAuth Client ID از نوع Web Application بسازید و مقدار Client ID را در فیلد بالا درج کنید.</p>
                                <p><strong>ربات بله:</strong> در پیام‌رسان بله به بازوی <code>@BotFather</code> پیام داده و ربات جدید بسازید و توکن را در فیلد قرار دهید.</p>
                                <p><strong>ربات تلگرام:</strong> در تلگرام با <code>@BotFather</code> ربات بسازید و توکن را درج فرمایید.</p>
                            </div>
                        </details>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CATEGORY 2: Finance, Payments & Wallet -->
            <!-- ========================================== -->
            <div id="cat-finance" class="main-cat-content hidden space-y-6">
                <!-- Layer 2 Sub-Tabs -->
                <div class="flex flex-wrap gap-2 p-1.5 bg-surface-container-low rounded-2xl w-fit">
                    <button type="button" onclick="switchSubTab('sub-c2c', this)" class="sub-tab active px-4 py-2 rounded-xl text-xs font-bold bg-white text-primary shadow-sm transition-all">
                        درگاه کارت‌به‌کارت و وب‌هوک بانکی
                    </button>
                    <button type="button" onclick="switchSubTab('sub-wallet-single', this)" class="sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        کیف پول، پاداش کش‌بک و شارژ دستی
                    </button>
                    <button type="button" onclick="switchSubTab('sub-wallet-bulk', this)" class="sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        شارژ / کسر گروهی موجودی + پیامک
                    </button>
                    <button type="button" onclick="switchSubTab('sub-wallet-logs', this)" class="sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        گردش حساب و لاگ تراکنش‌ها
                    </button>
                </div>

                <!-- Sub-tab 2.1: Card-to-Card Gateway -->
                <div id="sub-c2c" class="sub-content space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">credit_card</span>
                                    <span>تنظیمات درگاه اختصاصی کارت‌به‌کارت و وب‌هوک بانکی</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">مشخصات حساب بانکی جهت نمایش در تسویه‌حساب و تایید خودکار پیامک بانک.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                <span class="text-xs font-bold text-on-surface">فعالسازی درگاه کارت‌به‌کارت</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_c2c]" value="1" <?php checked(!empty($opt['enable_c2c'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">شماره کارت بانکی (۱۶ رقمی)</label>
                                <input type="text" name="settings[c2c_card_number]" value="<?php echo esc_attr($opt['c2c_card_number'] ?? '6037991122334455'); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-mono text-left" dir="ltr">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">نام صاحب حساب / کارت</label>
                                <input type="text" name="settings[c2c_card_holder]" value="<?php echo esc_attr($opt['c2c_card_holder'] ?? 'فروشگاه پالت'); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">نام بانک</label>
                                <input type="text" name="settings[c2c_bank_name]" value="<?php echo esc_attr($opt['c2c_bank_name'] ?? 'ملی'); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">شماره شبا (بدون IR)</label>
                                <input type="text" name="settings[c2c_sheba_number]" value="<?php echo esc_attr($opt['c2c_sheba_number'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-mono text-left" dir="ltr">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">کلید امنیتی وب‌هوک (Webhook Secret Key)</label>
                            <input type="text" name="settings[c2c_webhook_secret]" value="<?php echo esc_attr($opt['c2c_webhook_secret'] ?? 'palette_sec_key_123'); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-mono text-left" dir="ltr">
                        </div>

                        <!-- Dedicated Mobile App Connection & QR Code Card -->
                        <div class="bg-gradient-to-br from-slate-900 to-primary-dim text-white rounded-3xl p-6 shadow-xl relative overflow-hidden space-y-5">
                            <div class="absolute -top-12 -left-12 w-48 h-48 bg-primary/30 rounded-full blur-3xl pointer-events-none"></div>
                            
                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 relative z-10">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-emerald-400 border border-white/10 shadow-inner">
                                        <span class="material-symbols-outlined text-2xl">phonelink_ring</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm font-black text-white">اپلیکیشن اختصاصی موبایل (Palette SMS Relay)</h4>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">۱۰۰٪ متن‌باز و امن</span>
                                        </div>
                                        <p class="text-[11px] text-white/70 mt-0.5">دریافت آنی پیامک‌های واریز بانک و ارسال مستقیم به سایت شما با سیستم صف آفلاین</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-end md:self-auto">
                                    <a href="https://github.com/ketabchi-ar/palette-sms-relay/releases" target="_blank" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black px-4 py-2.5 rounded-xl text-xs flex items-center gap-1.5 shadow-lg transition-all">
                                        <span class="material-symbols-outlined text-base">download</span>
                                        <span>دانلود مستقیم فایل APK</span>
                                    </a>
                                </div>
                            </div>

                            <!-- QR Code & Setup Row -->
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 pt-4 border-t border-white/10 relative z-10">
                                <div class="lg:col-span-4 bg-white/10 backdrop-blur-md rounded-2xl p-4 flex flex-col items-center justify-center text-center border border-white/10">
                                    <?php 
                                    $qr_payload = json_encode([
                                        'app'                => 'palette-sms-relay',
                                        'site_name'          => get_bloginfo('name'),
                                        'site_url'           => home_url('/'),
                                        'webhook_url'        => rest_url('palette/v1/bank-sms-webhook'),
                                        'pending_orders_url' => rest_url('palette/v1/pending-card-orders'),
                                        'secret'             => $opt['c2c_webhook_secret'] ?? 'palette_sec_key_123'
                                    ], JSON_UNESCAPED_SLASHES);
                                    $qr_api_url = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . rawurlencode($qr_payload);
                                    ?>
                                    <div class="bg-white p-2.5 rounded-2xl shadow-lg mb-2.5">
                                        <img src="<?php echo esc_url($qr_api_url); ?>" alt="QR Code" class="w-36 h-36 object-contain rounded-lg">
                                    </div>
                                    <div class="text-[11px] font-bold text-white">اسکن با دوربین اپلیکیشن موبایل</div>
                                    <div class="text-[9px] text-white/60 mt-0.5">اتصال و تنظیم خودکار در ۱ ثانیه</div>
                                </div>

                                <div class="lg:col-span-8 space-y-3 text-xs text-white/80 leading-relaxed flex flex-col justify-center">
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-white/10 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">۱</span>
                                        <span>اپلیکیشن <strong>Palette SMS Relay</strong> را روی گوشی دریافت‌کننده پیامک بانک نصب نمایید.</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-white/10 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">۲</span>
                                        <span>دکمه <strong>اسکن QR Code</strong> در اپلیکیشن را زده و بارکد روبرو را اسکن کنید.</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-white/10 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">۳</span>
                                        <span>سرویس پس‌زمینه را فعال کنید؛ پیامک‌های واریزی بانک‌ها به طور خودکار دریافت شده و سفارشات تسویه می‌شوند.</span>
                                    </div>

                                    <div class="pt-2 flex flex-wrap gap-2">
                                        <div class="bg-white/5 rounded-xl px-3 py-1.5 border border-white/10 text-[10px] font-mono text-emerald-300">
                                            URL: <?php echo esc_html(rest_url('palette/v1/bank-sms-webhook')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Anti-Collision Unique Amount Settings -->
                        <div class="bg-surface-container-low/80 border border-outline-variant/30 rounded-2xl p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 font-bold text-xs text-on-surface">
                                    <span class="material-symbols-outlined text-primary text-lg">shuffle</span>
                                    <span>جلوگیری از تکرار مبالغ هم‌زمان (رندوم‌سازی ارقام آخر مبلغ)</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_c2c_random_cents]" value="1" <?php checked($opt['enable_c2c_random_cents'] ?? 0, 1); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <p class="text-xs text-on-surface-variant leading-relaxed">با فعال بودن این گزینه، سیستم هنگام انتخاب درگاه کارت‌به‌کارت مبلغی بسیار ناچیز و تصادفی (مثلاً ۳۷ تومان) به کل مبلغ سفارش اضافه می‌کند تا در صورت واریز همزمان چند کاربر، مبالغ کاملاً متمایز بوده و تطبیق خودکار پیامک بانک بدون کوچک‌ترین تداخل انجام شود.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-outline-variant/20">
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">میزان تغییر و ارقام رندوم</label>
                                    <select name="settings[c2c_random_digits]" class="w-full bg-white border border-outline-variant/30 rounded-2xl p-3 text-xs">
                                        <option value="1" <?php selected($opt['c2c_random_digits'] ?? 2, 1); ?>>۱ رقم (بین ۱ تا ۹ تومان)</option>
                                        <option value="2" <?php selected($opt['c2c_random_digits'] ?? 2, 2); ?>>۲ رقم (بین ۱۱ تا ۹۹ تومان - پیشنهادی)</option>
                                        <option value="3" <?php selected($opt['c2c_random_digits'] ?? 2, 3); ?>>۳ رقم (بین ۱۰۱ تا ۹۹۹ تومان)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">عنوان نمایش در فاکتور تسویه‌حساب</label>
                                    <input type="text" name="settings[c2c_random_fee_title]" value="<?php echo esc_attr($opt['c2c_random_fee_title'] ?? 'تطبیق هوشمند بانکی (شناسه رندوم)'); ?>" class="w-full bg-white border border-outline-variant/30 rounded-2xl p-3 text-xs">
                                </div>
                            </div>
                        </div>

                        <!-- Supported Banks Showcase -->
                        <div class="bg-surface-container-low/50 border border-outline-variant/30 rounded-2xl p-5 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 font-bold text-xs text-on-surface">
                                    <span class="material-symbols-outlined text-primary text-lg">domain</span>
                                    <span>بانک‌های پیش‌فرض و تحت پوشش سیستم (تطبیق هوشمند خودکار)</span>
                                </div>
                                <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">۱۷ بانک و شبکه شتاب</span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 pt-1">
                                <?php
                                $builtin_banks = class_exists('Palette_Panel_Card_To_Card') ? Palette_Panel_Card_To_Card::get_builtin_banks() : [];
                                foreach ($builtin_banks as $bb):
                                ?>
                                <div class="bg-white p-2.5 rounded-xl border border-outline-variant/20 flex items-center gap-2 shadow-xs">
                                    <span class="material-symbols-outlined text-primary text-base"><?php echo esc_html($bb['icon']); ?></span>
                                    <div class="truncate">
                                        <div class="text-[11px] font-bold text-on-surface truncate"><?php echo esc_html($bb['name']); ?></div>
                                        <div class="text-[9px] text-on-surface-variant truncate"><?php echo esc_html($bb['badge']); ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Smart Bank Pattern Learner & Custom Manager -->
                        <div class="bg-surface-container-lowest border-2 border-primary/20 rounded-2xl p-6 space-y-4 shadow-sm">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2 font-bold text-xs text-primary">
                                    <span class="material-symbols-outlined text-xl">psychology</span>
                                    <span>🧠 الگوساز و یادگیرنده هوشمند پیامک بانک‌های جدید</span>
                                </div>
                                <span class="text-[10px] bg-primary/10 text-primary px-3 py-1 rounded-full font-bold">Auto Regex Learner</span>
                            </div>
                            <p class="text-xs text-on-surface-variant leading-relaxed">اگر بانک یا مؤسسه‌ای پیامک متفاوتی ارسال می‌کند، کافیست نام بانک و نمونه پیامک دریافتی را در فرم زیر وارد کنید؛ سیستم الگوی ساختار آن را به صورت خودکار یاد گرفته و ذخیره می‌کند.</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">نام بانک یا سرویس مالی</label>
                                    <input type="text" id="learn-bank-name" placeholder="مثال: نئوبانک ویپاد، بانک سینا..." class="w-full bg-surface-container-low border-none rounded-xl p-3 text-xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">واحد پول پیامک</label>
                                    <select id="learn-currency-unit" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-xs">
                                        <option value="rial">ریال (تقسیم بر ۱۰ به تومان)</option>
                                        <option value="toman">تومان (مستقیم)</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" onclick="learnBankPattern()" id="btn-learn-pattern" class="w-full bg-primary hover:bg-primary-dim text-white p-3 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                                        <span class="material-symbols-outlined text-base">auto_awesome</span>
                                        <span>یادگیری و ثبت خودکار الگو</span>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">نمونه متن پیامک واریز بانک</label>
                                <textarea id="learn-sample-sms" rows="3" placeholder="متن پیامک دریافت شده از بانک را به طور کامل اینجا قرار دهید..." class="w-full bg-surface-container-low border-none rounded-xl p-3 text-xs font-mono"></textarea>
                            </div>

                            <div id="learn-pattern-feedback" class="hidden p-3.5 rounded-xl text-xs font-mono transition-all"></div>

                            <!-- List of Custom Learned Patterns -->
                            <?php
                            $custom_patterns = get_option('palette_custom_bank_patterns', []);
                            ?>
                            <div class="pt-3 border-t border-outline-variant/20 space-y-2">
                                <div class="text-xs font-bold text-on-surface">الگوهای اختصاصی یاد گرفته شده:</div>
                                <div id="custom-patterns-list" class="space-y-2">
                                    <?php if (empty($custom_patterns)): ?>
                                        <div class="p-3 bg-surface-container-low/40 rounded-xl text-xs text-on-surface-variant text-center" id="no-custom-patterns-msg">هنوز الگوی سفارشی ثبت نشده است.</div>
                                    <?php else: ?>
                                        <?php foreach ($custom_patterns as $cp): ?>
                                            <div class="p-3 bg-surface-container-low rounded-xl border border-outline-variant/20 flex justify-between items-center text-xs" id="pat-row-<?php echo esc_attr($cp['id']); ?>">
                                                <div class="space-y-1">
                                                    <span class="font-bold text-primary"><?php echo esc_html($cp['bank_name']); ?></span>
                                                    <span class="text-[10px] text-on-surface-variant bg-white px-2 py-0.5 rounded-md border border-outline-variant/20 mr-2">واحد: <?php echo $cp['unit'] === 'toman' ? 'تومان' : 'ریال'; ?></span>
                                                    <div class="font-mono text-[10px] text-slate-600 truncate max-w-md" dir="ltr"><?php echo esc_html($cp['regex']); ?></div>
                                                </div>
                                                <button type="button" onclick="deleteBankPattern('<?php echo esc_js($cp['id']); ?>')" class="text-rose-600 hover:bg-rose-50 p-1.5 rounded-lg text-xs font-bold flex items-center gap-1 cursor-pointer">
                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                    <span>حذف</span>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Live Webhook Test & Simulator -->
                        <div class="bg-surface-container-low/70 border border-outline-variant/30 rounded-2xl p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 font-bold text-xs text-on-surface">
                                    <span class="material-symbols-outlined text-primary text-lg">science</span>
                                    <span>🧪 شبیه‌ساز و تست زنده وب‌هوک پیامک بانک</span>
                                </div>
                                <span class="text-[10px] text-on-surface-variant bg-white px-2.5 py-1 rounded-full border border-outline-variant/20">تست آنلاین REST API</span>
                            </div>
                            <p class="text-xs text-on-surface-variant">برای اطمینان از عملکرد صحیح الگوی پیامک و وب‌هوک، یک نمونه متن پیامک واریز را وارد کرده و دکمه تست را بزنید:</p>
                            
                            <div class="space-y-2">
                                <textarea id="test-webhook-sms" rows="3" placeholder="واریز به حساب 1234 مبلغ 1000000 ریال پیگیری 987654321 بانک ملی..." class="w-full bg-white border border-outline-variant/30 rounded-2xl p-3 text-xs font-mono"></textarea>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button" onclick="testBankSmsWebhook()" id="btn-test-webhook" class="bg-primary hover:bg-primary-dim text-white px-5 py-2.5 rounded-xl font-bold text-xs transition-all flex items-center gap-2 cursor-pointer shadow-sm">
                                    <span class="material-symbols-outlined text-base">send</span>
                                    <span>ارسال درخواست تستی به وب‌هوک</span>
                                </button>
                            </div>

                            <div id="test-webhook-result" class="hidden p-4 rounded-xl text-xs font-mono transition-all"></div>
                        </div>
                    </div>
                </div>

                <!-- Sub-tab 2.2: Single User Wallet Adjustment -->
                <div id="sub-wallet-single" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
                                    <span>پیکربندی کیف پول، پاداش کش‌بک و شارژ اختصاصی</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">مدیریت نرخ کش‌بک و تغییر مستقیم اعتبار کاربران.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">درصد پاداش کش‌بک (Cashback) پایه هر سفارش</label>
                                <input type="number" name="settings[cashback_percent]" value="<?php echo esc_attr($opt['cashback_percent'] ?? 5); ?>" min="0" max="100" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-sm font-bold text-left font-mono" dir="ltr">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">حداقل مبلغ انتقال موجودی بین کاربران (تومان)</label>
                                <input type="number" name="settings[wallet_min_transfer]" value="<?php echo esc_attr($opt['wallet_min_transfer'] ?? 10000); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-sm text-left font-mono" dir="ltr">
                            </div>
                        </div>

                        <!-- Live User Search & Balance Tool -->
                        <div class="border-t border-outline-variant/20 pt-6 space-y-4">
                            <h4 class="text-sm font-bold text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-lg">manage_accounts</span>
                                <span>جستجوی زنده کاربر و تغییر دستی موجودی</span>
                            </h4>

                            <div class="bg-surface-container-low p-5 rounded-2xl space-y-4">
                                <div class="relative">
                                    <input type="text" id="admin-wallet-user-search" oninput="searchUsersForWallet(this.value)" placeholder="جستجوی نام، ایمیل، شماره همراه یا نام کاربری..." class="w-full bg-white border-none rounded-2xl p-3.5 pr-10 text-xs">
                                    <span class="material-symbols-outlined absolute right-3 text-on-surface-variant text-base">search</span>
                                    <div id="admin-wallet-user-results" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-outline-variant/40 rounded-2xl shadow-xl z-30 max-h-56 overflow-y-auto divide-y divide-outline-variant/20"></div>
                                </div>

                                <div id="admin-wallet-selected-card" class="hidden bg-white p-4 rounded-2xl border border-primary/30 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                    <div class="flex items-center gap-3">
                                        <img id="selected-user-avatar" class="w-10 h-10 rounded-full object-cover" src="" alt="Avatar">
                                        <div>
                                            <div class="font-bold text-xs text-on-surface" id="selected-user-name"></div>
                                            <div class="text-[10px] text-on-surface-variant font-mono mt-0.5" id="selected-user-info"></div>
                                        </div>
                                    </div>
                                    <div class="bg-primary-container/20 px-3.5 py-1.5 rounded-xl text-xs font-bold text-primary">
                                        <span>موجودی فعلی: </span>
                                        <strong id="selected-user-balance" class="font-mono"></strong> تومان
                                    </div>
                                </div>

                                <div id="admin-wallet-form-box" class="hidden space-y-4 pt-2">
                                    <input type="hidden" id="admin-wallet-selected-uid" value="">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-[11px] font-bold text-on-surface-variant mb-1">نوع عملیات</label>
                                            <select id="admin-wallet-action-type" class="w-full bg-white border-none rounded-2xl p-3 text-xs font-bold">
                                                <option value="add">➕ افزایش اعتبار (شارژ / واریز)</option>
                                                <option value="deduct">➖ کاهش اعتبار (برداشت / کسر)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-on-surface-variant mb-1">مبلغ (تومان)</label>
                                            <input type="number" id="admin-wallet-amount" placeholder="مثال: 50000" class="w-full bg-white border-none rounded-2xl p-3 text-xs font-mono font-bold text-left" dir="ltr">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-on-surface-variant mb-1">علت / توضیحات تراکنش</label>
                                            <input type="text" id="admin-wallet-reason" placeholder="مثال: هدیه مدیریت، جبران تاخیر و..." class="w-full bg-white border-none rounded-2xl p-3 text-xs">
                                        </div>
                                    </div>

                                    <button type="button" onclick="submitAdminWalletAdjustment()" id="btn-adjust-wallet" class="bg-primary hover:bg-primary-dim text-white px-6 py-3 rounded-2xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                                        <span class="material-symbols-outlined text-base">check_circle</span>
                                        <span>ثبت و اعمال تغییر موجودی</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-tab 2.3: Bulk Wallet Adjustment -->
                <div id="sub-wallet-bulk" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">groups</span>
                                <span>شارژ یا کسر اعتبار گروهی کاربران + ارسال پیامک خودکار</span>
                            </h3>
                            <p class="text-xs text-on-surface-variant mt-1">اعتبار کیف پول دسته‌ای از کاربران را تغییر دهید و پیامک اطلاع‌رسانی ارسال فرمایید.</p>
                        </div>

                        <div class="bg-surface-container-low p-6 rounded-2xl space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">گروه هدف کاربران</label>
                                    <select id="bulk-wallet-role" class="w-full bg-white border-none rounded-2xl p-3.5 text-xs font-bold">
                                        <option value="all">کلیه کاربران سایت (همه نقش‌ها)</option>
                                        <option value="customer">مشتریان ووکامرس (Customer)</option>
                                        <option value="subscriber">مشترکین سایت (Subscriber)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">نوع عملیات</label>
                                    <select id="bulk-wallet-action" class="w-full bg-white border-none rounded-2xl p-3.5 text-xs font-bold">
                                        <option value="add">➕ شارژ / افزایش اعتبار همگانی</option>
                                        <option value="deduct">➖ کسر اعتبار همگانی</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant mb-1">مبلغ به ازای هر کاربر (تومان)</label>
                                    <input type="number" id="bulk-wallet-amount" placeholder="مثال: 20000" class="w-full bg-white border-none rounded-2xl p-3.5 text-xs font-mono font-bold text-left" dir="ltr">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">علت / بابت تراکنش</label>
                                <input type="text" id="bulk-wallet-reason" placeholder="مثال: هدیه عید، جشنواره تخفیف و..." class="w-full bg-white border-none rounded-2xl p-3.5 text-xs">
                            </div>

                            <button type="button" onclick="submitBulkWalletAdjustment()" id="btn-bulk-wallet" class="bg-primary hover:bg-primary-dim text-white px-8 py-3.5 rounded-2xl text-xs font-bold shadow-md transition-all flex items-center gap-2 cursor-pointer">
                                <span class="material-symbols-outlined text-lg">bolt</span>
                                <span>شروع و اعمال عملیات گروهی</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sub-tab 2.4: Wallet Transactions Log -->
                <div id="sub-wallet-logs" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">receipt_long</span>
                            <span>گردش حساب و تاریخچه تراکنش‌های کیف پول</span>
                        </h3>

                        <?php
                        global $wpdb;
                        $tx_table = $is_woo_wallet ? ($wpdb->prefix . 'woo_wallet_transactions') : ($wpdb->prefix . 'serene_wallet_tx');
                        $recent_txs = [];
                        if ($wpdb->get_var("SHOW TABLES LIKE '{$tx_table}'") === $tx_table) {
                            $recent_txs = $wpdb->get_results("SELECT * FROM {$tx_table} ORDER BY 1 DESC LIMIT 50");
                        }
                        ?>

                        <?php if (empty($recent_txs)): ?>
                            <div class="text-center py-12 text-xs text-on-surface-variant">هنوز تراکنشی در سیستم ثبت نشده است.</div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-right border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-surface-container-low text-on-surface-variant font-bold border-b border-outline-variant/20">
                                            <th class="p-3">شناسه</th>
                                            <th class="p-3">کاربر</th>
                                            <th class="p-3">نوع تراکنش</th>
                                            <th class="p-3">مبلغ (تومان)</th>
                                            <th class="p-3">مانده جدید</th>
                                            <th class="p-3">علت / توضیحات</th>
                                            <th class="p-3">تاریخ و ساعت</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-outline-variant/20 font-medium">
                                        <?php foreach ($recent_txs as $tx): 
                                            $uid = $tx->user_id ?? 0;
                                            $u = get_user_by('ID', $uid);
                                            $is_credit = ($tx->type ?? '') === 'credit' || ($tx->amount ?? 0) > 0;
                                            $amount_val = abs(floatval($tx->amount ?? 0));
                                            $balance_val = floatval($tx->balance ?? ($tx->balance_after ?? 0));
                                            $desc_val = $tx->details ?? ($tx->description ?? '-');
                                            $date_val = $tx->date ?? ($tx->created_at ?? '');
                                        ?>
                                        <tr class="hover:bg-surface-container-low/40">
                                            <td class="p-3 font-mono text-primary font-bold">#<?php echo $tx->transaction_id ?? ($tx->id ?? '-'); ?></td>
                                            <td class="p-3 font-bold"><?php echo esc_html($u ? ($u->display_name ?: $u->user_login) : "کاربر #{$uid}"); ?></td>
                                            <td class="p-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold <?php echo $is_credit ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'; ?>">
                                                    <?php echo $is_credit ? 'واریز / شارژ' : 'برداشت / کسر'; ?>
                                                </span>
                                            </td>
                                            <td class="p-3 font-mono font-bold <?php echo $is_credit ? 'text-emerald-600' : 'text-rose-600'; ?>" dir="ltr">
                                                <?php echo ($is_credit ? '+' : '-') . number_format($amount_val); ?>
                                            </td>
                                            <td class="p-3 font-mono text-on-surface-variant" dir="ltr"><?php echo number_format($balance_val); ?></td>
                                            <td class="p-3"><?php echo esc_html($desc_val); ?></td>
                                            <td class="p-3 text-on-surface-variant font-mono"><?php echo esc_html(date_i18n('Y/m/d H:i', strtotime($date_val))); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CATEGORY 3: Design, Login & Menu Builder -->
            <!-- ========================================== -->
            <div id="cat-design" class="main-cat-content hidden space-y-6">
                <!-- Layer 2 Sub-Tabs -->
                <div class="flex flex-wrap gap-2 p-1.5 bg-surface-container-low rounded-2xl w-fit">
                    <button type="button" onclick="switchSubTab('sub-login-customizer', this)" class="sub-tab active px-4 py-2 rounded-xl text-xs font-bold bg-white text-primary shadow-sm transition-all">
                        دیزاین و شخصی‌سازی صفحه ورود (۴ طرح)
                    </button>
                    <button type="button" onclick="switchSubTab('sub-admin-theme', this)" class="sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        پوسته پیشخوان مدیریت وردپرس (Admin Skins)
                    </button>
                    <button type="button" onclick="switchSubTab('sub-colors', this)" class="sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        پالت رنگ و استایل سراسری
                    </button>
                    <button type="button" onclick="switchSubTab('sub-menu', this)" class="sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        منوساز زنده داشبورد + آیکون‌پیکر
                    </button>
                    <button type="button" onclick="switchSubTab('sub-form', this)" class="sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        فرم‌ساز فیلدهای اختصاصی
                    </button>
                </div>

                <!-- Sub-tab 3.1: Login Page Customizer -->
                <div id="sub-login-customizer" class="sub-content space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">lock_open</span>
                                <span>شخصی‌سازی و انتخاب طرح بصری صفحه ورود (Login Customizer)</span>
                            </h3>
                            <p class="text-xs text-on-surface-variant mt-1">طرح دلخواه صفحه ورود را انتخاب کرده و لوگو، پس‌زمینه و دسترسی نقش‌ها را شخصی‌سازی کنید.</p>
                        </div>

                        <!-- 4 Interactive Visual Layout Selector Cards -->
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-3">انتخاب طرح بصری صفحه ورود:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4" id="login-layout-cards">
                                <?php $current_login_theme = $opt['login_theme_style'] ?? 'split_modern'; ?>
                                <!-- Layout 1: Split Modern -->
                                <div onclick="selectLayoutCard('split_modern', this)" class="layout-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_login_theme === 'split_modern' ? 'border-primary bg-primary/5 shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 hover:border-primary/40'; ?>" data-val="split_modern">
                                    <div class="layout-check-badge <?php echo $current_login_theme === 'split_modern' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="h-20 rounded-xl bg-gradient-to-r from-primary to-slate-800 flex items-center justify-center text-white">
                                            <span class="material-symbols-outlined text-3xl">view_column</span>
                                        </div>
                                        <div class="font-bold text-xs text-on-surface">۱. طرح مدرن دوتکه (Split)</div>
                                        <p class="text-[10px] text-on-surface-variant">ستون بنر برندینگ + ستون فرم ورود</p>
                                    </div>
                                </div>

                                <!-- Layout 2: Glassmorphism -->
                                <div onclick="selectLayoutCard('glass_minimal', this)" class="layout-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_login_theme === 'glass_minimal' ? 'border-primary bg-primary/5 shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 hover:border-primary/40'; ?>" data-val="glass_minimal">
                                    <div class="layout-check-badge <?php echo $current_login_theme === 'glass_minimal' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="h-20 rounded-xl bg-gradient-to-tr from-sky-400 via-indigo-300 to-pink-300 flex items-center justify-center text-white shadow-inner">
                                            <div class="w-10 h-12 bg-white/60 backdrop-blur-md rounded-lg border border-white/50"></div>
                                        </div>
                                        <div class="font-bold text-xs text-on-surface">۲. طرح شیشه‌ای (Glass)</div>
                                        <p class="text-[10px] text-on-surface-variant">کارت مرکزی شیشه‌ای با افکت بلور مدرن</p>
                                    </div>
                                </div>

                                <!-- Layout 3: Dark SaaS -->
                                <div onclick="selectLayoutCard('dark_modern', this)" class="layout-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_login_theme === 'dark_modern' ? 'border-primary bg-primary/5 shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 hover:border-primary/40'; ?>" data-val="dark_modern">
                                    <div class="layout-check-badge <?php echo $current_login_theme === 'dark_modern' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="h-20 rounded-xl bg-[#0b0f19] border border-slate-700 flex items-center justify-center text-primary">
                                            <span class="material-symbols-outlined text-3xl">dark_mode</span>
                                        </div>
                                        <div class="font-bold text-xs text-on-surface">۳. طرح دارک (Dark SaaS)</div>
                                        <p class="text-[10px] text-on-surface-variant">تم دارک لوکس با رنگ‌بندی نئونی</p>
                                    </div>
                                </div>

                                <!-- Layout 4: Floating Card -->
                                <div onclick="selectLayoutCard('floating_card', this)" class="layout-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_login_theme === 'floating_card' ? 'border-primary bg-primary/5 shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 hover:border-primary/40'; ?>" data-val="floating_card">
                                    <div class="layout-check-badge <?php echo $current_login_theme === 'floating_card' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="h-20 rounded-xl bg-slate-100 flex items-center justify-center text-primary">
                                            <span class="material-symbols-outlined text-3xl">crop_portrait</span>
                                        </div>
                                        <div class="font-bold text-xs text-on-surface">۴. کارت شناور (Floating)</div>
                                        <p class="text-[10px] text-on-surface-variant">کارت مرکزی با سایه عمیق متریال ۳</p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="settings[login_theme_style]" id="input-login-theme-style" value="<?php echo esc_attr($current_login_theme); ?>">
                        </div>

                        <!-- Live Color Overrides for Login Page -->
                        <div class="p-5 bg-surface-container-low rounded-2xl space-y-4 border border-outline-variant/30">
                            <div class="flex justify-between items-center flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-base">palette</span>
                                    <h4 class="text-xs font-bold text-on-surface">شخصی‌سازی زنده رنگ‌های قالب لاگین انتخابی (Live Color Overrides)</h4>
                                </div>
                                <button type="button" onclick="suggestPalette('login')" class="bg-primary/10 hover:bg-primary/20 text-primary border border-primary/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-xs">
                                    <span class="material-symbols-outlined text-base text-primary animate-pulse">auto_awesome</span>
                                    <span>✨ پیشنهاد پالت اتوماتیک</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="bg-white p-3.5 rounded-xl border border-outline-variant/20 flex items-center justify-between">
                                    <div>
                                        <div class="text-[11px] font-bold text-on-surface">رنگ اصلی دکمه‌ها</div>
                                        <div class="text-[9px] text-on-surface-variant">Primary Button</div>
                                    </div>
                                    <input type="color" name="settings[login_custom_primary]" value="<?php echo esc_attr($opt['login_custom_primary'] ?? '#4c5e8b'); ?>" class="w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
                                </div>
                                <div class="bg-white p-3.5 rounded-xl border border-outline-variant/20 flex items-center justify-between">
                                    <div>
                                        <div class="text-[11px] font-bold text-on-surface">پس‌زمینه صفحه</div>
                                        <div class="text-[9px] text-on-surface-variant">Background Color</div>
                                    </div>
                                    <input type="color" name="settings[login_custom_bg_color]" value="<?php echo esc_attr($opt['login_custom_bg_color'] ?? ($opt['login_custom_bg'] ?? '#f8fafc')); ?>" class="w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
                                </div>
                                <div class="bg-white p-3.5 rounded-xl border border-outline-variant/20 flex items-center justify-between">
                                    <div>
                                        <div class="text-[11px] font-bold text-on-surface">کارت و باکس ورود</div>
                                        <div class="text-[9px] text-on-surface-variant">Card Background</div>
                                    </div>
                                    <input type="color" name="settings[login_custom_card]" value="<?php echo esc_attr($opt['login_custom_card'] ?? '#ffffff'); ?>" class="w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
                                </div>
                                <div class="bg-white p-3.5 rounded-xl border border-outline-variant/20 flex items-center justify-between">
                                    <div>
                                        <div class="text-[11px] font-bold text-on-surface">رنگ متون و عناوین</div>
                                        <div class="text-[9px] text-on-surface-variant">Text & Headings</div>
                                    </div>
                                    <input type="color" name="settings[login_custom_text]" value="<?php echo esc_attr($opt['login_custom_text'] ?? '#0f172a'); ?>" class="w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Media Library Uploaders for Logo & Background -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Logo Upload -->
                            <div class="p-5 bg-surface-container-low rounded-2xl space-y-3">
                                <label class="block text-xs font-bold text-on-surface">لوگوی اختصاصی صفحه ورود</label>
                                <div class="flex gap-2">
                                    <input type="text" name="settings[login_custom_logo]" id="input-login-logo" value="<?php echo esc_attr($opt['login_custom_logo'] ?? ''); ?>" placeholder="آدرس تصویر لوگو..." class="w-full bg-white border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                    <button type="button" onclick="openMediaUploader('input-login-logo', 'preview-login-logo')" class="bg-primary hover:bg-primary-dim text-white px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-1.5 cursor-pointer whitespace-nowrap shadow-sm">
                                        <span class="material-symbols-outlined text-base">cloud_upload</span>
                                        <span>بارگذاری</span>
                                    </button>
                                </div>
                                <div id="preview-login-logo" class="h-14 <?php echo empty($opt['login_custom_logo']) ? 'hidden' : ''; ?> flex items-center p-2 bg-white rounded-2xl border border-outline-variant/30">
                                    <img src="<?php echo esc_url($opt['login_custom_logo'] ?? ''); ?>" class="max-h-full object-contain">
                                </div>
                            </div>

                            <!-- Background Upload -->
                            <div class="p-5 bg-surface-container-low rounded-2xl space-y-3">
                                <label class="block text-xs font-bold text-on-surface">تصویر پس‌زمینه دلخواه صفحه ورود</label>
                                <div class="flex gap-2">
                                    <input type="text" name="settings[login_custom_bg]" id="input-login-bg" value="<?php echo esc_attr($opt['login_custom_bg'] ?? ''); ?>" placeholder="آدرس تصویر پس‌زمینه..." class="w-full bg-white border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                    <button type="button" onclick="openMediaUploader('input-login-bg', 'preview-login-bg')" class="bg-primary hover:bg-primary-dim text-white px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-1.5 cursor-pointer whitespace-nowrap shadow-sm">
                                        <span class="material-symbols-outlined text-base">cloud_upload</span>
                                        <span>بارگذاری</span>
                                    </button>
                                </div>
                                <div id="preview-login-bg" class="h-14 <?php echo empty($opt['login_custom_bg']) ? 'hidden' : ''; ?> flex items-center p-2 bg-white rounded-2xl border border-outline-variant/30">
                                    <img src="<?php echo esc_url($opt['login_custom_bg'] ?? ''); ?>" class="max-h-full max-w-full object-cover rounded-xl">
                                </div>
                            </div>
                        </div>

                        <!-- Role Targeting & Titles -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">عنوان خوش‌آمدگویی (Welcome Title)</label>
                                <input type="text" name="settings[login_welcome_title]" value="<?php echo esc_attr($opt['login_welcome_title'] ?? ('ورود به ' . get_bloginfo('name'))); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">توضیحات زیر عنوان</label>
                                <input type="text" name="settings[login_welcome_subtitle]" value="<?php echo esc_attr($opt['login_welcome_subtitle'] ?? 'جهت دسترسی به حساب کاربری وارد شوید'); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">هدایت به صفحه ورود سفارشی برای:</label>
                                <select name="settings[login_role_target]" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-bold">
                                    <option value="all" <?php selected($opt['login_role_target'] ?? 'all', 'all'); ?>>همه کاربران سایت (شامل مدیران)</option>
                                    <option value="customers_only" <?php selected($opt['login_role_target'] ?? '', 'customers_only'); ?>>فقط مشتریان (مدیران به wp-login هدایت شوند)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-tab 3.2: WordPress Admin Dashboard Skins -->
                <div id="sub-admin-theme" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">dashboard_customize</span>
                                <span>پوسته‌های مدرن پیشخوان مدیریت وردپرس (WP Admin Dashboard Skins)</span>
                            </h3>
                            <p class="text-xs text-on-surface-variant mt-1">ظاهر کلی سایدبار و نوار بالای پیشخوان وردپرس را با فونت وزیرمتن و پالت‌های مدرن تغییر دهید.</p>
                        </div>

                        <?php $current_admin_skin = $opt['admin_dashboard_theme'] ?? 'palette_slate'; ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4" id="admin-skin-cards">
                            <div onclick="selectSkinCard('palette_slate', this)" class="skin-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_admin_skin === 'palette_slate' ? 'border-primary bg-primary/5 shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 hover:border-primary/40'; ?>" data-val="palette_slate">
                                <div class="skin-check-badge <?php echo $current_admin_skin === 'palette_slate' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="h-16 rounded-xl bg-[#1e293b] border border-slate-700 flex items-center justify-center text-white">
                                        <span class="font-bold text-xs">Palette Slate Pro</span>
                                    </div>
                                    <div class="font-bold text-xs text-on-surface">پالت اسلیت پرو (پیشنهادی)</div>
                                    <p class="text-[10px] text-on-surface-variant">سایدبار تیره شیک با تایپوگرافی وزیرمتن</p>
                                </div>
                            </div>

                            <div onclick="selectSkinCard('clean_saas', this)" class="skin-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_admin_skin === 'clean_saas' ? 'border-primary bg-primary/5 shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 hover:border-primary/40'; ?>" data-val="clean_saas">
                                <div class="skin-check-badge <?php echo $current_admin_skin === 'clean_saas' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="h-16 rounded-xl bg-[#f8fafc] border border-slate-200 flex items-center justify-center text-slate-800">
                                        <span class="font-bold text-xs">Clean SaaS Light</span>
                                    </div>
                                    <div class="font-bold text-xs text-on-surface">کلین ساس سفید</div>
                                    <p class="text-[10px] text-on-surface-variant">ظاهر روشن، مدرن و مینیمال</p>
                                </div>
                            </div>

                            <div onclick="selectSkinCard('midnight_dark', this)" class="skin-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_admin_skin === 'midnight_dark' ? 'border-primary bg-primary/5 shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 hover:border-primary/40'; ?>" data-val="midnight_dark">
                                <div class="skin-check-badge <?php echo $current_admin_skin === 'midnight_dark' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="h-16 rounded-xl bg-[#0b0f19] border border-blue-900 flex items-center justify-center text-sky-400">
                                        <span class="font-bold text-xs">Midnight Dark</span>
                                    </div>
                                    <div class="font-bold text-xs text-on-surface">دارک مود نیمه‌شب</div>
                                    <p class="text-[10px] text-on-surface-variant">پوسته تاریک حرفه‌ای</p>
                                </div>
                            </div>

                            <div onclick="selectSkinCard('wp_default', this)" class="skin-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_admin_skin === 'wp_default' ? 'border-primary bg-primary/5 shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 hover:border-primary/40'; ?>" data-val="wp_default">
                                <div class="skin-check-badge <?php echo $current_admin_skin === 'wp_default' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="h-16 rounded-xl bg-[#23282d] flex items-center justify-center text-white">
                                        <span class="font-bold text-xs">WordPress Core</span>
                                    </div>
                                    <div class="font-bold text-xs text-on-surface">پیش‌فرض وردپرس</div>
                                    <p class="text-[10px] text-on-surface-variant">بدون تغییر در استایل ادمین</p>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="settings[admin_dashboard_theme]" id="input-admin-dashboard-theme" value="<?php echo esc_attr($current_admin_skin); ?>">

                        <!-- Live Color Overrides for Admin Theme -->
                        <div class="p-5 bg-surface-container-low rounded-2xl space-y-4 border border-outline-variant/30">
                            <div class="flex justify-between items-center flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-base">palette</span>
                                    <h4 class="text-xs font-bold text-on-surface">شخصی‌سازی زنده رنگ‌های پوسته پیشخوان مدیریت (Admin Skin Overrides)</h4>
                                </div>
                                <button type="button" onclick="suggestPalette('admin')" class="bg-primary/10 hover:bg-primary/20 text-primary border border-primary/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-xs">
                                    <span class="material-symbols-outlined text-base text-primary animate-pulse">auto_awesome</span>
                                    <span>✨ پیشنهاد پالت اتوماتیک</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-white p-3.5 rounded-xl border border-outline-variant/20 flex items-center justify-between">
                                    <div>
                                        <div class="text-[11px] font-bold text-on-surface">رنگ سایدبار ادمین</div>
                                        <div class="text-[9px] text-on-surface-variant">Sidebar Background</div>
                                    </div>
                                    <input type="color" name="settings[admin_custom_sidebar_bg]" value="<?php echo esc_attr($opt['admin_custom_sidebar_bg'] ?? '#1e293b'); ?>" class="w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
                                </div>
                                <div class="bg-white p-3.5 rounded-xl border border-outline-variant/20 flex items-center justify-between">
                                    <div>
                                        <div class="text-[11px] font-bold text-on-surface">رنگ هایلایت و منوی فعال</div>
                                        <div class="text-[9px] text-on-surface-variant">Active Menu Accent</div>
                                    </div>
                                    <input type="color" name="settings[admin_custom_accent]" value="<?php echo esc_attr($opt['admin_custom_accent'] ?? '#3b82f6'); ?>" class="w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
                                </div>
                                <div class="bg-white p-3.5 rounded-xl border border-outline-variant/20 flex items-center justify-between">
                                    <div>
                                        <div class="text-[11px] font-bold text-on-surface">رنگ نوار ابزار بالا</div>
                                        <div class="text-[9px] text-on-surface-variant">Topbar Background</div>
                                    </div>
                                    <input type="color" name="settings[admin_custom_topbar_bg]" value="<?php echo esc_attr($opt['admin_custom_topbar_bg'] ?? '#0f172a'); ?>" class="w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Global Input Field Design Styles (3 Models) -->
                        <div class="p-6 bg-surface-container-low rounded-2xl space-y-4 border border-outline-variant/30">
                            <div class="flex justify-between items-center flex-wrap gap-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary text-base">crop_free</span>
                                        <h4 class="text-xs font-bold text-on-surface">طراحی و استایل فیلدهای ورودی (Input Fields Design System)</h4>
                                    </div>
                                    <p class="text-[10px] text-on-surface-variant mt-0.5">سبک گوشه‌ها، حاشیه و افکت فیلدهای متنی در کل پنل مدیریت و سایت را انتخاب فرمایید.</p>
                                </div>
                                <span class="text-[10px] bg-primary/10 text-primary px-3 py-1 rounded-full font-bold">۳ سبک دیزاین نرم</span>
                            </div>

                            <?php $current_input_style = $opt['input_field_style'] ?? 'soft'; ?>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="input-style-cards">
                                <!-- Model 1: Pill & Ultra-Curved -->
                                <div onclick="selectInputStyle('pill', this)" class="input-style-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_input_style === 'pill' ? 'border-primary bg-white shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 bg-white hover:border-primary/40'; ?>" data-val="pill">
                                    <div class="input-check-badge <?php echo $current_input_style === 'pill' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="font-bold text-xs text-on-surface">مدل ۱: کپسولی و فوق‌العاده نرم (Pill)</div>
                                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/60 space-y-2">
                                            <div class="h-9 px-4 rounded-full bg-white border border-slate-300 text-[11px] text-slate-700 flex items-center shadow-xs">نمونه فیلد کپسولی...</div>
                                        </div>
                                        <p class="text-[10px] text-on-surface-variant">گوشه‌های کاملاً گرد کپسولی (Pill Rounded) با حاشیه ملایم</p>
                                    </div>
                                </div>

                                <!-- Model 2: Modern Soft 2XL (Default & Recommended) -->
                                <div onclick="selectInputStyle('soft', this)" class="input-style-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_input_style === 'soft' ? 'border-primary bg-white shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 bg-white hover:border-primary/40'; ?>" data-val="soft">
                                    <div class="input-check-badge <?php echo $current_input_style === 'soft' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="font-bold text-xs text-on-surface">مدل ۲: ارگونومیک مدرن (Soft 2XL - پیشنهادی)</div>
                                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/60 space-y-2">
                                            <div class="h-9 px-3 rounded-2xl bg-white border border-slate-300 text-[11px] text-slate-700 flex items-center shadow-xs">نمونه فیلد ارگونومیک...</div>
                                        </div>
                                        <p class="text-[10px] text-on-surface-variant">گوشه‌های لطیف ۱۶ پیکسلی (rounded-2xl) با افکت فوکوس هاله نوری</p>
                                    </div>
                                </div>

                                <!-- Model 3: Frosted Glass & Neu-Soft -->
                                <div onclick="selectInputStyle('glass', this)" class="input-style-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between <?php echo $current_input_style === 'glass' ? 'border-primary bg-white shadow-md ring-2 ring-primary/20' : 'border-outline-variant/30 bg-white hover:border-primary/40'; ?>" data-val="glass">
                                    <div class="input-check-badge <?php echo $current_input_style === 'glass' ? '' : 'hidden'; ?> absolute top-3 left-3 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md">
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="font-bold text-xs text-on-surface">مدل ۳: شیشه‌ای نئومورفیک (Frosted Glass)</div>
                                        <div class="p-3 bg-slate-100/70 rounded-2xl border border-slate-200/60 space-y-2">
                                            <div class="h-9 px-3 rounded-xl bg-white/80 backdrop-blur-md border border-slate-300/80 text-[11px] text-slate-700 flex items-center shadow-inner">نمونه فیلد شیشه‌ای...</div>
                                        </div>
                                        <p class="text-[10px] text-on-surface-variant">پس‌زمینه نیمه‌شفاف مات، حاشیه شیشه‌ای و سایه داخلی عمیق</p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="settings[input_field_style]" id="input-field-style" value="<?php echo esc_attr($current_input_style); ?>">
                        </div>
                    </div>
                </div>

                <!-- Sub-tab 3.3: Colors & Styling -->
                <div id="sub-colors" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">palette</span>
                            <span>شخصی‌سازی پالت رنگ سراسری و آواتار پیش‌فرض کاربران</span>
                        </h3>

                        <!-- Site Default Avatar Uploader -->
                        <div class="p-5 bg-surface-container-low rounded-2xl space-y-3 border border-outline-variant/20">
                            <label class="block text-xs font-bold text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-base">account_circle</span>
                                <span>تصویر آواتار پیش‌فرض کاربران (Site Default Avatar)</span>
                            </label>
                            <p class="text-[11px] text-on-surface-variant">در صورتی که کاربر تصویر پروفایل بارگذاری نکرده باشد، این تصویر به جای گراواتار نمایش داده خواهد شد.</p>
                            <div class="flex gap-2">
                                <input type="text" name="settings[default_avatar_url]" id="input-default-avatar" value="<?php echo esc_attr($opt['default_avatar_url'] ?? ''); ?>" placeholder="آدرس تصویر آواتار پیش‌فرض..." class="w-full bg-white border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                <button type="button" onclick="openMediaUploader('input-default-avatar', 'preview-default-avatar')" class="bg-primary hover:bg-primary-dim text-white px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-1.5 cursor-pointer whitespace-nowrap shadow-sm">
                                    <span class="material-symbols-outlined text-base">cloud_upload</span>
                                    <span>بارگذاری</span>
                                </button>
                            </div>
                            <div id="preview-default-avatar" class="h-14 <?php echo empty($opt['default_avatar_url']) ? 'hidden' : ''; ?> flex items-center p-2 bg-white rounded-2xl border border-outline-variant/30">
                                <img src="<?php echo esc_url($opt['default_avatar_url'] ?? ''); ?>" class="w-10 h-10 rounded-full object-cover">
                            </div>
                        </div>

                        <div class="flex justify-between items-center flex-wrap gap-2 pt-2">
                            <h4 class="text-xs font-bold text-on-surface">پالت رنگ اختصاصی سراسری افزونه (Global Colors)</h4>
                            <button type="button" onclick="suggestPalette('global')" class="bg-primary/10 hover:bg-primary/20 text-primary border border-primary/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-xs">
                                <span class="material-symbols-outlined text-base text-primary animate-pulse">auto_awesome</span>
                                <span>✨ پیشنهاد پالت اتوماتیک</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-surface-container-low p-5 rounded-2xl flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-on-surface">رنگ اصلی (Primary)</label>
                                    <span class="text-[11px] text-on-surface-variant">دکمه‌ها و عناصر شاخص</span>
                                </div>
                                <input type="color" name="settings[color_primary]" value="<?php echo esc_attr($opt['color_primary'] ?? '#4c5e8b'); ?>" class="w-12 h-10 p-0 border-none rounded-xl cursor-pointer">
                            </div>

                            <div class="bg-surface-container-low p-5 rounded-2xl flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-on-surface">رنگ ثانویه (Secondary)</label>
                                    <span class="text-[11px] text-on-surface-variant">بج‌ها و تگ‌های فرعی</span>
                                </div>
                                <input type="color" name="settings[color_secondary]" value="<?php echo esc_attr($opt['color_secondary'] ?? '#5b5f71'); ?>" class="w-12 h-10 p-0 border-none rounded-xl cursor-pointer">
                            </div>

                            <div class="bg-surface-container-low p-5 rounded-2xl flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-on-surface">رنگ پاداش و ویژه (Accent)</label>
                                    <span class="text-[11px] text-on-surface-variant">هدایا و گردونه شانس</span>
                                </div>
                                <input type="color" name="settings[color_tertiary]" value="<?php echo esc_attr($opt['color_tertiary'] ?? '#77527e'); ?>" class="w-12 h-10 p-0 border-none rounded-xl cursor-pointer">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-2">کدهای CSS سفارشی (Custom CSS)</label>
                            <textarea name="settings[custom_css]" rows="6" placeholder="/* استایل‌های اختصاصی خود را در این بخش بنویسید */" class="w-full bg-surface-container-low border-none rounded-2xl p-4 text-xs font-mono text-left" dir="ltr"><?php echo esc_textarea($opt['custom_css'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Sub-tab 3.4: Live Menu Builder with Icon Picker -->
                <div id="sub-menu" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">menu</span>
                                    <span>منوساز زنده داشبورد با انتخابگر بصری آیکون (Icon Picker)</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">ترتیب، عناوین، آیکون‌ها و دسترسی سایدبار پیشخوان کاربران را مدیریت کنید.</p>
                            </div>
                            <button type="button" onclick="addNewMenuItem()" class="bg-primary hover:bg-primary-dim text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                                <span class="material-symbols-outlined text-base">add</span>
                                <span>افزودن آیتم جدید به منو</span>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse" id="menu-builder-table">
                                <thead>
                                    <tr class="bg-surface-container-low text-xs font-bold text-on-surface-variant">
                                        <th class="p-3 text-center">فعال</th>
                                        <th class="p-3">شناسه</th>
                                        <th class="p-3">عنوان نمایشی</th>
                                        <th class="p-3">آیکون</th>
                                        <th class="p-3">نوع آیتم</th>
                                        <th class="p-3">مقصد / لینک</th>
                                        <th class="p-3">بج</th>
                                        <th class="p-3">دسترسی نقش</th>
                                        <th class="p-3 text-center">ترتیب</th>
                                        <th class="p-3 text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20 text-xs">
                                    <?php foreach ($menu_items as $key => $item): ?>
                                    <tr class="menu-row">
                                        <td class="p-2 text-center">
                                            <input type="checkbox" name="menu_items[<?php echo esc_attr($key); ?>][enabled]" value="1" <?php checked(!empty($item['enabled'])); ?> class="rounded text-primary">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="menu_items[<?php echo esc_attr($key); ?>][id]" value="<?php echo esc_attr($item['id'] ?? $key); ?>" class="w-24 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono" readonly>
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="menu_items[<?php echo esc_attr($key); ?>][title]" value="<?php echo esc_attr($item['title'] ?? ''); ?>" class="w-32 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
                                        </td>
                                        <td class="p-2">
                                            <div class="flex items-center gap-1.5">
                                                <input type="hidden" name="menu_items[<?php echo esc_attr($key); ?>][icon]" value="<?php echo esc_attr($item['icon'] ?? 'star'); ?>" class="row-icon-input">
                                                <button type="button" onclick="openIconPicker(this)" class="bg-surface-container-low hover:bg-surface-container px-2.5 py-1.5 rounded-xl border border-outline-variant/30 flex items-center gap-1.5 cursor-pointer text-xs font-bold">
                                                    <span class="material-symbols-outlined text-primary text-base row-icon-preview"><?php echo esc_html($item['icon'] ?? 'star'); ?></span>
                                                    <span class="text-[10px] text-on-surface-variant font-mono"><?php echo esc_html($item['icon'] ?? 'star'); ?></span>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="p-2">
                                            <select name="menu_items[<?php echo esc_attr($key); ?>][type]" class="bg-surface-container-low border-none rounded-xl p-2 text-xs">
                                                <option value="endpoint" <?php selected($item['type'] ?? 'endpoint', 'endpoint'); ?>>تب داخلی</option>
                                                <option value="custom_link" <?php selected($item['type'] ?? '', 'custom_link'); ?>>لینک دلخواه</option>
                                                <option value="shortcode" <?php selected($item['type'] ?? '', 'shortcode'); ?>>شورت‌کد</option>
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="menu_items[<?php echo esc_attr($key); ?>][target]" value="<?php echo esc_attr($item['target'] ?? ''); ?>" class="w-32 bg-surface-container-low border-none rounded-xl p-2.5 text-xs text-left" dir="ltr">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="menu_items[<?php echo esc_attr($key); ?>][badge]" value="<?php echo esc_attr($item['badge'] ?? ''); ?>" class="w-16 bg-surface-container-low border-none rounded-xl p-2.5 text-xs">
                                        </td>
                                        <td class="p-2">
                                            <select name="menu_items[<?php echo esc_attr($key); ?>][roles]" class="bg-surface-container-low border-none rounded-xl p-2 text-xs">
                                                <option value="all" <?php selected($item['roles'] ?? 'all', 'all'); ?>>همه کاربران</option>
                                                <option value="customer" <?php selected($item['roles'] ?? '', 'customer'); ?>>فقط مشتریان</option>
                                                <option value="administrator" <?php selected($item['roles'] ?? '', 'administrator'); ?>>فقط مدیران</option>
                                            </select>
                                        </td>
                                        <td class="p-2 text-center">
                                            <input type="number" name="menu_items[<?php echo esc_attr($key); ?>][order]" value="<?php echo esc_attr($item['order'] ?? 10); ?>" class="w-12 bg-surface-container-low border-none rounded-xl p-2 text-xs text-center" dir="ltr">
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف آیتم">
                                                <span class="material-symbols-outlined text-lg leading-none">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sub-tab 3.5: Form Builder -->
                <div id="sub-form" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">badge</span>
                                    <span>فرم‌ساز فیلدهای اختصاصی پروفایل و تسویه‌حساب</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">فیلدهای دلخواه مانند کد ملی، تاریخ تولد، شماره شبا را ایجاد نمایید.</p>
                            </div>
                            <button type="button" onclick="addNewFormField()" class="bg-primary hover:bg-primary-dim text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                                <span class="material-symbols-outlined text-base">add</span>
                                <span>افزودن فیلد جدید</span>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse" id="form-builder-table">
                                <thead>
                                    <tr class="bg-surface-container-low text-xs font-bold text-on-surface-variant">
                                        <th class="p-3">کلید فیلد (Meta Key)</th>
                                        <th class="p-3">برچسب نمایشی</th>
                                        <th class="p-3">نوع فیلد</th>
                                        <th class="p-3 text-center">ضروری</th>
                                        <th class="p-3 text-center">نمایش در ثبت‌نام</th>
                                        <th class="p-3 text-center">نمایش در تسویه</th>
                                        <th class="p-3 text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20 text-xs">
                                    <?php foreach ($custom_fields as $key => $f): ?>
                                    <tr class="form-row hover:bg-surface-container-low/40 transition-colors">
                                        <td class="p-3">
                                            <input type="text" name="custom_fields[<?php echo esc_attr($key); ?>][key]" value="<?php echo esc_attr($f['key'] ?? $key); ?>" placeholder="مثال: national_code" class="w-full min-w-[130px] bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-left" dir="ltr" required>
                                        </td>
                                        <td class="p-3">
                                            <input type="text" name="custom_fields[<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($f['label'] ?? ''); ?>" placeholder="مثال: کد ملی" class="w-full min-w-[140px] bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
                                        </td>
                                        <td class="p-3">
                                            <select name="custom_fields[<?php echo esc_attr($key); ?>][type]" class="w-full min-w-[140px] bg-surface-container-low border-none rounded-xl p-2 text-xs">
                                                <option value="text" <?php selected($f['type'] ?? 'text', 'text'); ?>>متن کوتاه (Text)</option>
                                                <option value="number" <?php selected($f['type'] ?? '', 'number'); ?>>عددی (Number)</option>
                                                <option value="date" <?php selected($f['type'] ?? '', 'date'); ?>>تاریخ (Date)</option>
                                                <option value="textarea" <?php selected($f['type'] ?? '', 'textarea'); ?>>متن بلند (Textarea)</option>
                                            </select>
                                        </td>
                                        <td class="p-3 text-center">
                                            <label class="relative inline-flex items-center justify-center cursor-pointer">
                                                <input type="checkbox" name="custom_fields[<?php echo esc_attr($key); ?>][required]" value="1" <?php checked(!empty($f['required'])); ?> class="sr-only peer">
                                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all after:shadow-xs peer-checked:bg-primary"></div>
                                            </label>
                                        </td>
                                        <td class="p-3 text-center">
                                            <label class="relative inline-flex items-center justify-center cursor-pointer">
                                                <input type="checkbox" name="custom_fields[<?php echo esc_attr($key); ?>][show_in_register]" value="1" <?php checked(!empty($f['show_in_register'])); ?> class="sr-only peer">
                                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all after:shadow-xs peer-checked:bg-primary"></div>
                                            </label>
                                        </td>
                                        <td class="p-3 text-center">
                                            <label class="relative inline-flex items-center justify-center cursor-pointer">
                                                <input type="checkbox" name="custom_fields[<?php echo esc_attr($key); ?>][show_in_checkout]" value="1" <?php checked(!empty($f['show_in_checkout'])); ?> class="sr-only peer">
                                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all after:shadow-xs peer-checked:bg-primary"></div>
                                            </label>
                                        </td>
                                        <td class="p-3 text-center">
                                            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف فیلد">
                                                <span class="material-symbols-outlined text-lg leading-none">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CATEGORY 4: Engagement & Gamification -->
            <!-- ========================================== -->
            <div id="cat-engagement" class="main-cat-content hidden space-y-6">
                <!-- Layer 2 Sub-Tabs -->
                <div class="flex flex-wrap gap-2 p-1.5 bg-surface-container-low rounded-2xl w-fit">
                    <button type="button" onclick="switchSubTab('sub-wheel', this)" class="sub-tab active px-4 py-2 rounded-xl text-xs font-bold bg-white text-primary shadow-sm transition-all">
                        گردونه شانس و جوایز پویا
                    </button>
                    <button type="button" onclick="switchSubTab('sub-reviews', this)" class="sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        نقد و بررسی هوشمند و پاداش
                    </button>
                    <button type="button" onclick="switchSubTab('sub-chat', this)" class="sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        گفتگوی زنده و پشتیبانی شناور
                    </button>
                </div>

                <!-- Sub-tab 4.1: Lucky Wheel -->
                <div id="sub-wheel" class="sub-content space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">casino</span>
                                    <span>مدیریت گردونه شانس، جوایز و کدهای تخفیف پویا</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">تنظیم اسلایس‌ها، ضرایب شانس و روزهای اعتبار کدهای تخفیف.</p>
                            </div>
                            <button type="button" onclick="addNewWheelSlice()" class="bg-primary hover:bg-primary-dim text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                                <span class="material-symbols-outlined text-base">add</span>
                                <span>افزودن اسلایس جدید</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-4 border-b border-outline-variant/20">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-on-surface">فعالسازی ماژول گردونه شانس</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_lucky_wheel]" value="1" <?php checked(!empty($opt['enable_lucky_wheel'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">سقف چرخش روزانه هر کاربر</label>
                                <input type="number" name="settings[lucky_wheel_daily_limit]" value="<?php echo esc_attr($opt['lucky_wheel_daily_limit'] ?? 1); ?>" min="1" max="10" class="w-full bg-surface-container-low border-none rounded-2xl p-3 text-xs text-left font-mono" dir="ltr">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">مدت اعتبار کدهای تخفیف (روز)</label>
                                <input type="number" name="settings[lucky_wheel_coupon_expiry_days]" value="<?php echo esc_attr($opt['lucky_wheel_coupon_expiry_days'] ?? 7); ?>" min="1" max="365" class="w-full bg-surface-container-low border-none rounded-2xl p-3 text-xs text-left font-mono" dir="ltr">
                            </div>
                        </div>

                        <!-- Slices Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse" id="wheel-slices-table">
                                <thead>
                                    <tr class="bg-surface-container-low text-xs font-bold text-on-surface-variant">
                                        <th class="p-3">عنوان روی اسلایس</th>
                                        <th class="p-3">نوع جایزه</th>
                                        <th class="p-3">مقدار / درصد</th>
                                        <th class="p-3 text-center">وزن شانس (Weight)</th>
                                        <th class="p-3 text-center">احتمال برد (٪)</th>
                                        <th class="p-3 text-center">رنگ پس‌زمینه</th>
                                        <th class="p-3 text-center">رنگ متن</th>
                                        <th class="p-3 text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20 text-xs">
                                    <?php foreach ($wheel_slices as $idx => $slice): ?>
                                    <tr class="slice-row">
                                        <td class="p-2">
                                            <input type="text" name="wheel_slices[<?php echo $idx; ?>][label]" value="<?php echo esc_attr($slice['label']); ?>" class="w-full bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
                                        </td>
                                        <td class="p-2">
                                            <select name="wheel_slices[<?php echo $idx; ?>][type]" class="bg-surface-container-low border-none rounded-xl p-2 text-xs">
                                                <option value="coupon" <?php selected($slice['type'], 'coupon'); ?>>کد تخفیف اختصاصی</option>
                                                <option value="wallet" <?php selected($slice['type'], 'wallet'); ?>>شارژ کیف پول (تومان)</option>
                                                <option value="empty" <?php selected($slice['type'], 'empty'); ?>>پوچ / دوباره بچرخ</option>
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="wheel_slices[<?php echo $idx; ?>][value]" value="<?php echo esc_attr($slice['value']); ?>" placeholder="20% یا 50000" class="w-28 bg-surface-container-low border-none rounded-xl p-2.5 text-xs text-left font-mono" dir="ltr">
                                        </td>
                                        <td class="p-2 text-center">
                                            <input type="number" name="wheel_slices[<?php echo $idx; ?>][weight]" value="<?php echo esc_attr($slice['weight'] ?? 10); ?>" min="1" max="200" oninput="calculateWheelProbabilities()" class="w-16 bg-surface-container-low border-none rounded-xl p-2 text-xs text-center slice-weight-input font-mono" dir="ltr">
                                        </td>
                                        <td class="p-2 text-center font-bold font-mono text-primary slice-prob-display">--%</td>
                                        <td class="p-2 text-center">
                                            <input type="color" name="wheel_slices[<?php echo $idx; ?>][color]" value="<?php echo esc_attr($slice['color'] ?? '#4c5e8b'); ?>" class="w-9 h-8 p-0 border-none rounded cursor-pointer">
                                        </td>
                                        <td class="p-2 text-center">
                                            <input type="color" name="wheel_slices[<?php echo $idx; ?>][text]" value="<?php echo esc_attr($slice['text'] ?? '#ffffff'); ?>" class="w-9 h-8 p-0 border-none rounded cursor-pointer">
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف اسلایس">
                                                <span class="material-symbols-outlined text-lg leading-none">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Dynamic Lucky Wheel Coupons History & Management -->
                        <?php
                        global $wpdb;
                        $table_wheel = $wpdb->prefix . 'serene_lucky_wheel_spins';
                        $table_users = $wpdb->prefix . 'users';
                        $wheel_history = [];
                        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_wheel}'") === $table_wheel) {
                            $wheel_history = $wpdb->get_results(
                                "SELECT s.*, u.display_name, u.user_login, u.user_email 
                                 FROM {$table_wheel} s 
                                 LEFT JOIN {$table_users} u ON s.user_id = u.ID 
                                 ORDER BY s.id DESC LIMIT 100"
                            );
                        }
                        $total_spins = count($wheel_history);
                        $total_coupons = count(array_filter($wheel_history, fn($r) => !empty($r->coupon_code)));
                        ?>
                        <div class="mt-8 pt-6 border-t border-outline-variant/20 space-y-4">
                            <div class="flex justify-between items-center flex-wrap gap-4">
                                <div>
                                    <h4 class="text-sm font-bold text-on-surface flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary text-lg">loyalty</span>
                                        <span>کدهای تخفیف پویا و تاریخچه جوایز برندگان گردونه شانس</span>
                                    </h4>
                                    <p class="text-xs text-on-surface-variant mt-0.5">مشاهده مشخصات کاربران برنده، کدهای تخفیف سیستمی تولید شده، انقضا و وضعیت استفاده در سفارشات</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs bg-primary/10 text-primary px-3 py-1.5 rounded-xl font-bold">
                                        کل برندگان: <?php echo number_format_i18n($total_spins); ?> نفر
                                    </span>
                                    <button type="button" onclick="clearExpiredWheelCoupons()" class="bg-rose-50 hover:bg-rose-100 text-rose-700 px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1 cursor-pointer border border-rose-200">
                                        <span class="material-symbols-outlined text-sm">cleaning_services</span>
                                        <span>پاکسازی منقضی‌شده‌ها</span>
                                    </button>
                                </div>
                            </div>

                            <div class="overflow-x-auto bg-surface-container-low/50 rounded-2xl border border-outline-variant/30">
                                <table class="w-full text-right border-collapse text-xs" id="wheel-coupons-table">
                                    <thead>
                                        <tr class="bg-surface-container-low text-[11px] font-bold text-on-surface-variant border-b border-outline-variant/20">
                                            <th class="p-3 text-center">شناسه</th>
                                            <th class="p-3">کاربر برنده</th>
                                            <th class="p-3">نوع جایزه</th>
                                            <th class="p-3">کد تخفیف اختصاصی</th>
                                            <th class="p-3">ارزش جایزه</th>
                                            <th class="p-3">تاریخ برد</th>
                                            <th class="p-3">تاریخ انقضا</th>
                                            <th class="p-3 text-center">وضعیت استفاده</th>
                                            <th class="p-3 text-center">حذف</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-outline-variant/20">
                                        <?php if (empty($wheel_history)): ?>
                                            <tr>
                                                <td colspan="9" class="p-6 text-center text-on-surface-variant">هنوز هیچ چرخشی در گردونه شانس ثبت نشده است.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php 
                                            $now_ts = current_time('timestamp');
                                            foreach ($wheel_history as $wh): 
                                                $exp_ts = !empty($wh->expires_at) ? strtotime($wh->expires_at) : 0;
                                                $is_expired = ($exp_ts > 0 && $exp_ts < $now_ts);
                                                $is_used = false;
                                                if (!empty($wh->coupon_code) && class_exists('WC_Coupon')) {
                                                    $cid = wc_get_coupon_id_by_code($wh->coupon_code);
                                                    if ($cid) {
                                                        $c_obj = new WC_Coupon($cid);
                                                        $is_used = ($c_obj->get_usage_count() > 0);
                                                    }
                                                }
                                                if ($wh->is_claimed == 1) $is_used = true;
                                            ?>
                                            <tr class="hover:bg-white/80 transition-colors" id="wheel-row-<?php echo esc_attr($wh->id); ?>">
                                                <td class="p-3 text-center font-mono text-[11px] text-slate-500">#<?php echo esc_html($wh->id); ?></td>
                                                <td class="p-3 font-bold text-on-surface">
                                                    <div><?php echo esc_html($wh->display_name ?: ($wh->user_login ?: 'کاربر شماره ' . $wh->user_id)); ?></div>
                                                    <div class="text-[10px] text-slate-400 font-normal"><?php echo esc_html($wh->user_email ?: ''); ?></div>
                                                </td>
                                                <td class="p-3">
                                                    <?php if ($wh->prize_type === 'coupon'): ?>
                                                        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg font-bold border border-blue-200">کوپن تخفیف</span>
                                                    <?php elseif ($wh->prize_type === 'wallet'): ?>
                                                        <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg font-bold border border-emerald-200">شارژ کیف پول</span>
                                                    <?php else: ?>
                                                        <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg font-bold">پوچ</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="p-3 font-mono text-left" dir="ltr">
                                                    <?php if (!empty($wh->coupon_code)): ?>
                                                        <span class="bg-white px-2.5 py-1 rounded-lg border border-outline-variant/40 font-bold text-primary inline-flex items-center gap-1.5 shadow-xs">
                                                            <span><?php echo esc_html($wh->coupon_code); ?></span>
                                                            <span onclick="navigator.clipboard.writeText('<?php echo esc_js($wh->coupon_code); ?>'); showAdminNotification('کد تخفیف کپی شد.', 'success');" class="material-symbols-outlined text-xs text-slate-400 hover:text-primary cursor-pointer" title="کپی کد">content_copy</span>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-slate-400 text-[11px]">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="p-3 font-bold text-on-surface">
                                                    <?php echo esc_html($wh->prize_value); ?>
                                                </td>
                                                <td class="p-3 text-[11px] text-on-surface-variant font-mono">
                                                    <?php echo esc_html(function_exists('wp_date') ? wp_date('Y/m/d H:i', strtotime($wh->created_at)) : $wh->created_at); ?>
                                                </td>
                                                <td class="p-3 text-[11px] font-mono">
                                                    <?php if ($exp_ts > 0): ?>
                                                        <span class="<?php echo $is_expired ? 'text-rose-600 font-bold' : 'text-slate-700'; ?>">
                                                            <?php echo esc_html(function_exists('wp_date') ? wp_date('Y/m/d', $exp_ts) : $wh->expires_at); ?>
                                                        </span>
                                                        <?php if ($is_expired): ?>
                                                            <span class="text-[9px] bg-rose-50 text-rose-600 px-1.5 py-0.5 rounded border border-rose-200 mr-1">منقضی</span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-slate-400">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="p-3 text-center">
                                                    <?php if ($wh->prize_type === 'empty'): ?>
                                                        <span class="text-slate-400">-</span>
                                                    <?php elseif ($is_used): ?>
                                                        <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full text-[10px] font-bold border border-emerald-200 flex items-center gap-1 justify-center w-fit mx-auto">
                                                            <span class="material-symbols-outlined text-xs">done_all</span>
                                                            <span>استفاده شده</span>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full text-[10px] font-bold border border-amber-200 flex items-center gap-1 justify-center w-fit mx-auto">
                                                            <span class="material-symbols-outlined text-xs">schedule</span>
                                                            <span>آماده مصرف</span>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="p-3 text-center">
                                                    <button type="button" onclick="deleteWheelCoupon(<?php echo esc_js($wh->id); ?>)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg material-symbols-outlined text-base cursor-pointer transition-colors" title="حذف رکورد">delete</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-tab 4.2: Smart Reviews -->
                <div id="sub-reviews" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">rate_review</span>
                                    <span>نقد و بررسی هوشمند و پاداش کیف پول</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">تشویق خریداران به ثبت تجربه خرید و عکس محصول با شارژ خودکار کیف پول.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                <span class="text-xs font-bold text-on-surface">فعالسازی پاداش دیدگاه</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_smart_reviews]" value="1" <?php checked(!empty($opt['enable_smart_reviews'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">مبلغ پاداش کیف پول به ازای هر نظر (تومان)</label>
                                <input type="number" name="settings[review_reward_amount]" value="<?php echo esc_attr($opt['review_reward_amount'] ?? 10000); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left font-mono font-bold" dir="ltr">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">حداقل امتیاز برای دریافت پاداش</label>
                                <select name="settings[review_min_rating]" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-bold">
                                    <option value="1" <?php selected($opt['review_min_rating'] ?? 3, 1); ?>>۱ ستاره به بالا</option>
                                    <option value="3" <?php selected($opt['review_min_rating'] ?? 3, 3); ?>>۳ ستاره به بالا</option>
                                    <option value="4" <?php selected($opt['review_min_rating'] ?? 3, 4); ?>>۴ ستاره به بالا</option>
                                    <option value="5" <?php selected($opt['review_min_rating'] ?? 3, 5); ?>>فقط ۵ ستاره</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-3 pt-6">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[review_require_purchase]" value="1" <?php checked(!empty($opt['review_require_purchase'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                                <span class="text-xs font-bold text-on-surface">فقط به خریداران واقعی محصول پاداش داده شود</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-tab 4.3: Live Chat -->
                <div id="sub-chat" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">forum</span>
                                    <span>ماژول گفتگوی زنده چندکاناله پویا (Live Chat Multi-Channel Builder)</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">کانال‌های ارتباطی دلخواه (واتساپ، تلگرام، ایتا، روبیکا، بله، تماس، تیکت و پیامک) را به ویجت شناور سایت اضافه کنید.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                    <span class="text-xs font-bold text-on-surface">فعالسازی ویجت شناور</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[enable_live_chat]" value="1" <?php checked(!empty($opt['enable_live_chat'])); ?> class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <button type="button" onclick="addNewChatChannelRow()" class="bg-primary hover:bg-primary-dim text-white px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    <span>افزودن کانال ارتباطی</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pb-4 border-b border-outline-variant/20">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">موقعیت دکمه در صفحه</label>
                                <select name="settings[chat_position]" class="w-full bg-surface-container-low border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3.5 text-xs font-bold">
                                    <option value="left" <?php selected($opt['chat_position'] ?? 'left', 'left'); ?>>گوشه پایین چپ (پیشنهادی)</option>
                                    <option value="right" <?php selected($opt['chat_position'] ?? '', 'right'); ?>>گوشه پایین راست</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">عنوان هدر ویجت</label>
                                <input type="text" name="settings[chat_title]" value="<?php echo esc_attr($opt['chat_title'] ?? 'پشتیبانی آنلاین و راهنما'); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3.5 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">متن زیر عنوان</label>
                                <input type="text" name="settings[chat_subtitle]" value="<?php echo esc_attr($opt['chat_subtitle'] ?? 'ما همیشه آماده پاسخگویی به شما هستیم'); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 focus:border-primary focus:ring-4 focus:ring-primary/10 rounded-2xl p-3.5 text-xs">
                            </div>
                        </div>

                        <!-- Dynamic Channels Table -->
                        <?php $active_chat_channels = Palette_Panel_Live_Chat::get_channels(); ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse" id="chat-channels-table">
                                <thead>
                                    <tr class="bg-surface-container-low text-xs font-bold text-on-surface-variant">
                                        <th class="p-3 text-center">فعال</th>
                                        <th class="p-3">عنوان کانال</th>
                                        <th class="p-3">توضیح کوتاه / ساعت پاسخگویی</th>
                                        <th class="p-3">نوع کانال</th>
                                        <th class="p-3">مقصد / شماره / آیدی / لینک</th>
                                        <th class="p-3">آیکون</th>
                                        <th class="p-3 text-center">رنگ برند</th>
                                        <th class="p-3 text-center">ترتیب</th>
                                        <th class="p-3 text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20 text-xs">
                                    <?php foreach ($active_chat_channels as $cid => $ch): ?>
                                    <tr class="chat-ch-row">
                                        <td class="p-2 text-center">
                                            <input type="checkbox" name="chat_channels[<?php echo esc_attr($cid); ?>][enabled]" value="1" <?php checked(!empty($ch['enabled'])); ?> class="rounded text-primary">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="chat_channels[<?php echo esc_attr($cid); ?>][title]" value="<?php echo esc_attr($ch['title']); ?>" class="w-36 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="chat_channels[<?php echo esc_attr($cid); ?>][subtitle]" value="<?php echo esc_attr($ch['subtitle'] ?? ''); ?>" placeholder="پاسخگویی سریع..." class="w-36 bg-surface-container-low border-none rounded-xl p-2.5 text-xs">
                                        </td>
                                        <td class="p-2">
                                            <select name="chat_channels[<?php echo esc_attr($cid); ?>][type]" class="bg-surface-container-low border-none rounded-xl p-2 text-xs font-bold">
                                                <option value="ticket" <?php selected($ch['type'] ?? '', 'ticket'); ?>>🎫 تیکت پشتیبانی</option>
                                                <option value="whatsapp" <?php selected($ch['type'] ?? '', 'whatsapp'); ?>>💬 واتساپ</option>
                                                <option value="telegram" <?php selected($ch['type'] ?? '', 'telegram'); ?>>✈️ تلگرام</option>
                                                <option value="phone" <?php selected($ch['type'] ?? '', 'phone'); ?>>📞 تماس مستقیم</option>
                                                <option value="sms" <?php selected($ch['type'] ?? '', 'sms'); ?>>✉️ پیامک</option>
                                                <option value="custom" <?php selected($ch['type'] ?? '', 'custom'); ?>>🔗 لینک دلخواه (ایتا، روبیکا...)</option>
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="chat_channels[<?php echo esc_attr($cid); ?>][url]" value="<?php echo esc_attr($ch['url'] ?? ''); ?>" placeholder="98912... یا https://..." class="w-44 bg-surface-container-low border-none rounded-xl p-2.5 text-xs text-left font-mono" dir="ltr">
                                        </td>
                                        <td class="p-2">
                                            <div class="flex items-center gap-1">
                                                <input type="hidden" name="chat_channels[<?php echo esc_attr($cid); ?>][icon]" value="<?php echo esc_attr($ch['icon'] ?? 'chat'); ?>" class="row-icon-input">
                                                <button type="button" onclick="openIconPicker(this)" class="bg-surface-container-low hover:bg-surface-container px-2 py-1.5 rounded-xl border border-outline-variant/30 flex items-center gap-1 cursor-pointer">
                                                    <span class="material-symbols-outlined text-primary text-base row-icon-preview"><?php echo esc_html($ch['icon'] ?? 'chat'); ?></span>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="p-2 text-center">
                                            <input type="color" name="chat_channels[<?php echo esc_attr($cid); ?>][color]" value="<?php echo esc_attr($ch['color'] ?? '#4c5e8b'); ?>" class="w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
                                        </td>
                                        <td class="p-2 text-center">
                                            <input type="number" name="chat_channels[<?php echo esc_attr($cid); ?>][order]" value="<?php echo esc_attr($ch['order'] ?? 10); ?>" class="w-12 bg-surface-container-low border-none rounded-xl p-2 text-xs text-center" dir="ltr">
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف کانال">
                                                <span class="material-symbols-outlined text-lg leading-none">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CATEGORY 5: Advanced Features (10 Modules) -->
            <!-- ========================================== -->
            <div id="cat-advanced" class="main-cat-content hidden space-y-6">
                <!-- Layer 2 Sub-Tabs for all 10 modules -->
                <div class="flex flex-wrap gap-2 p-1.5 bg-surface-container-low rounded-2xl w-fit">
                    <button type="button" onclick="switchSubTab('sub-feat-invoice', this)" class="sub-tab active px-3.5 py-2 rounded-xl text-xs font-bold bg-white text-primary shadow-sm transition-all">
                        ۱. فاکتورساز رسمی
                    </button>
                    <button type="button" onclick="switchSubTab('sub-feat-carriers', this)" class="sub-tab px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        ۲. شرکت‌های پستی
                    </button>
                    <button type="button" onclick="switchSubTab('sub-feat-loyalty', this)" class="sub-tab px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        ۳. باشگاه مشتریان پویا
                    </button>
                    <button type="button" onclick="switchSubTab('sub-feat-price-alerts', this)" class="sub-tab px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        ۴. هشدار قیمت و موجودی
                    </button>
                    <button type="button" onclick="switchSubTab('sub-feat-affiliate', this)" class="sub-tab px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        ۵. همکاری در فروش
                    </button>
                    <button type="button" onclick="switchSubTab('sub-feat-rma', this)" class="sub-tab px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        ۶. مرجوعی کالا (RMA)
                    </button>
                    <button type="button" onclick="switchSubTab('sub-feat-shahkar', this)" class="sub-tab px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        ۷. شاهکار (کدملی)
                    </button>
                    <button type="button" onclick="switchSubTab('sub-feat-pwa', this)" class="sub-tab px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        ۸. وب‌اپلیکیشن (PWA)
                    </button>
                    <button type="button" onclick="switchSubTab('sub-feat-sessions', this)" class="sub-tab px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        ۹. نشست‌های فعال
                    </button>
                    <button type="button" onclick="switchSubTab('sub-feat-licenses', this)" class="sub-tab px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all">
                        ۱۰. لایسنس و دانلود
                    </button>
                </div>

                <!-- 1. Invoice Sub-tab -->
                <div id="sub-feat-invoice" class="sub-content space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">receipt_long</span>
                                    <span>۱. فاکتورساز رسمی منطبق بر استانداردهای مالیاتی ایران</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">مشخصات حقوقی، لوگو و متن یادداشت فاکتورهای قابل چاپ سفارشات ووکامرس.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                <span class="text-xs font-bold text-on-surface">فعالسازی فاکتورساز</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_pdf_invoice]" value="1" <?php checked(!empty($opt['enable_pdf_invoice'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">نام رسمی فروشگاه / شرکت</label>
                                <input type="text" name="settings[invoice_seller_name]" value="<?php echo esc_attr($opt['invoice_seller_name'] ?? get_bloginfo('name')); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">آدرس لوگوی فاکتور</label>
                                <div class="flex gap-2">
                                    <input type="text" name="settings[invoice_logo_url]" id="input-invoice-logo" value="<?php echo esc_attr($opt['invoice_logo_url'] ?? ''); ?>" placeholder="URL لوگو..." class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left font-mono" dir="ltr">
                                    <button type="button" onclick="openMediaUploader('input-invoice-logo', null)" class="bg-primary text-white px-3 py-2 rounded-xl text-xs font-bold cursor-pointer">
                                        <span class="material-symbols-outlined text-sm">cloud_upload</span>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">رنگ تم و هدر فاکتور</label>
                                <div class="flex items-center gap-3 bg-surface-container-low p-2 rounded-2xl">
                                    <input type="color" name="settings[invoice_color]" value="<?php echo esc_attr($opt['invoice_color'] ?? '#4c5e8b'); ?>" class="w-10 h-8 p-0 border-none rounded-lg cursor-pointer">
                                    <span class="text-xs font-mono font-bold"><?php echo esc_html($opt['invoice_color'] ?? '#4c5e8b'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">کد اقتصادی فروشنده</label>
                                <input type="text" name="settings[invoice_economic_code]" value="<?php echo esc_attr($opt['invoice_economic_code'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-mono text-left" dir="ltr">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">شناسه ملی / شماره ثبت</label>
                                <input type="text" name="settings[invoice_national_id]" value="<?php echo esc_attr($opt['invoice_national_id'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-mono text-left" dir="ltr">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">شماره تماس پشتیبانی</label>
                                <input type="text" name="settings[invoice_phone]" value="<?php echo esc_attr($opt['invoice_phone'] ?? ''); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs text-left" dir="ltr">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">یادداشت و متن پاورقی فاکتور</label>
                            <input type="text" name="settings[invoice_footer_note]" value="<?php echo esc_attr($opt['invoice_footer_note'] ?? 'از خرید و اعتماد شما به فروشگاه ما سپاسگزاریم.'); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs">
                        </div>
                    </div>
                </div>

                <!-- 2. Post Tracker Sub-tab -->
                <div id="sub-feat-carriers" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">local_shipping</span>
                                    <span>۲. مدیریت شرکت‌های پستی و رهگیری زنده مرسولات</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">تعریف شرکت‌های حمل‌ونقل (پست پیشتاز، تیپاکس، چاپار، ماهکس، اسنپ‌باکس و...) با الگوی لینک پیگیری.</p>
                            </div>
                            <button type="button" onclick="addNewCarrierRow()" class="bg-primary hover:bg-primary-dim text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                                <span class="material-symbols-outlined text-base">add</span>
                                <span>افزودن شرکت جدید</span>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse" id="carriers-table">
                                <thead>
                                    <tr class="bg-surface-container-low text-xs font-bold text-on-surface-variant">
                                        <th class="p-3">شناسه انگلیسی</th>
                                        <th class="p-3">نام شرکت حمل‌ونقل</th>
                                        <th class="p-3">الگوی لینک رهگیری زنده</th>
                                        <th class="p-3 text-center">آیکون</th>
                                        <th class="p-3 text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20 text-xs">
                                    <?php foreach ($carriers as $slug => $c): ?>
                                    <tr class="carrier-row" data-slug="<?php echo esc_attr($slug); ?>">
                                        <td class="p-2">
                                            <input type="text" value="<?php echo esc_attr($slug); ?>" class="carrier-slug-input w-28 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono" readonly>
                                        </td>
                                        <td class="p-2">
                                            <input type="text" value="<?php echo esc_attr($c['name']); ?>" class="carrier-name-input w-48 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
                                        </td>
                                        <td class="p-2">
                                            <input type="text" value="<?php echo esc_attr($c['url']); ?>" placeholder="https://tracking.example.com/?id={code}" class="carrier-url-input w-full bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-left" dir="ltr">
                                        </td>
                                        <td class="p-2 text-center">
                                            <input type="text" value="<?php echo esc_attr($c['icon'] ?? 'local_shipping'); ?>" class="carrier-icon-input w-24 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-center" dir="ltr">
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف شرکت پستی">
                                                <span class="material-symbols-outlined text-lg leading-none">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" onclick="saveCarriersList()" id="btn-save-carriers" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-base">check</span>
                            <span>ذخیره شرکت‌های پستی</span>
                        </button>
                    </div>
                </div>

                <!-- 3. Dynamic Loyalty Tiers Sub-tab -->
                <div id="sub-feat-loyalty" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">stars</span>
                                    <span>۳. باشگاه مشتریان و سطوح وفاداری پویا (Dynamic Loyalty Tiers)</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">تعریف نامحدود رده‌های کاربری بر اساس میانگین خرید بازه زمانی دلخواه، کش‌بک و مزایا.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                    <span class="text-xs font-bold text-on-surface">فعالسازی باشگاه مشتریان</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[enable_loyalty_tiers]" value="1" <?php checked(!empty($opt['enable_loyalty_tiers'])); ?> class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <button type="button" onclick="addNewLoyaltyTierRow()" class="bg-primary hover:bg-primary-dim text-white px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    <span>افزودن سطح جدید</span>
                                </button>
                            </div>
                        </div>

                        <!-- Dynamic Tiers Builder Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse" id="loyalty-tiers-table">
                                <thead>
                                    <tr class="bg-surface-container-low text-xs font-bold text-on-surface-variant">
                                        <th class="p-3">عنوان رده</th>
                                        <th class="p-3">آیکون</th>
                                        <th class="p-3">رنگ بج</th>
                                        <th class="p-3">حداقل خرید (تومان)</th>
                                        <th class="p-3">بازه زمانی بررسی</th>
                                        <th class="p-3">حداقل سفارشات</th>
                                        <th class="p-3">کش‌بک (٪)</th>
                                        <th class="p-3">تخفیف سفارش (٪)</th>
                                        <th class="p-3">مزایای سطح</th>
                                        <th class="p-3 text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20 text-xs">
                                    <?php foreach ($loyalty_tiers as $t_id => $tier): ?>
                                    <tr class="tier-row" data-id="<?php echo esc_attr($t_id); ?>">
                                        <td class="p-2">
                                            <input type="text" value="<?php echo esc_attr($tier['name']); ?>" class="tier-name-input w-28 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
                                        </td>
                                        <td class="p-2">
                                            <div class="flex items-center gap-1">
                                                <input type="hidden" value="<?php echo esc_attr($tier['icon'] ?? 'stars'); ?>" class="tier-icon-input row-icon-input">
                                                <button type="button" onclick="openIconPicker(this)" class="bg-surface-container-low hover:bg-surface-container px-2 py-1.5 rounded-xl border border-outline-variant/30 flex items-center gap-1 cursor-pointer">
                                                    <span class="material-symbols-outlined text-primary text-base row-icon-preview"><?php echo esc_html($tier['icon'] ?? 'stars'); ?></span>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="p-2">
                                            <input type="color" value="<?php echo esc_attr($tier['color'] ?? '#cd7f32'); ?>" class="tier-color-input w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" value="<?php echo esc_attr($tier['min_spent'] ?? 0); ?>" class="tier-spent-input w-28 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-left" dir="ltr">
                                        </td>
                                        <td class="p-2">
                                            <select class="tier-lookback-input bg-surface-container-low border-none rounded-xl p-2 text-xs font-bold">
                                                <option value="0" <?php selected($tier['lookback_months'] ?? 0, 0); ?>>کل تاریخچه</option>
                                                <option value="1" <?php selected($tier['lookback_months'] ?? 0, 1); ?>>۱ ماه اخیر</option>
                                                <option value="3" <?php selected($tier['lookback_months'] ?? 0, 3); ?>>۳ ماه اخیر</option>
                                                <option value="6" <?php selected($tier['lookback_months'] ?? 0, 6); ?>>۶ ماه اخیر (پیشنهادی)</option>
                                                <option value="12" <?php selected($tier['lookback_months'] ?? 0, 12); ?>>۱۲ ماه اخیر</option>
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" value="<?php echo esc_attr($tier['min_orders'] ?? 0); ?>" class="tier-orders-input w-16 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-center" dir="ltr">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" value="<?php echo esc_attr($tier['cashback_percent'] ?? 1); ?>" class="tier-cashback-input w-16 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-center font-bold text-emerald-600" dir="ltr">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" value="<?php echo esc_attr($tier['discount'] ?? 0); ?>" class="tier-discount-input w-16 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-center font-bold text-primary" dir="ltr">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" value="<?php echo esc_attr($tier['perks'] ?? ''); ?>" placeholder="توضیح کوتاه مزایا..." class="tier-perks-input w-44 bg-surface-container-low border-none rounded-xl p-2.5 text-xs">
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف سطح">
                                                <span class="material-symbols-outlined text-lg leading-none">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" onclick="saveLoyaltyTiersList()" id="btn-save-tiers" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-base">check</span>
                            <span>ذخیره سطوح باشگاه مشتریان</span>
                        </button>
                    </div>
                </div>

                <!-- 4. Price & Stock Alerts Sub-tab -->
                <div id="sub-feat-price-alerts" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">notifications_active</span>
                                    <span>۴. اطلاع‌رسانی خودکار کاهش قیمت و موجودی کالا (Price & Stock Alerts)</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">نمایش دکمه‌های «موجود شد خبرم کن» و «ارزان شد خبرم کن» در صفحه محصول با ارسال پیامک هوشمند.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                <span class="text-xs font-bold text-on-surface">فعالسازی ماژول اطلاع‌رسانی</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_price_alerts]" value="1" <?php checked(!empty($opt['enable_price_alerts'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Accordion Guide for Price Alerts -->
                        <details class="group bg-blue-50/80 border border-blue-200/80 rounded-2xl p-4 transition-all duration-300">
                            <summary class="flex justify-between items-center cursor-pointer font-bold text-xs text-blue-900 list-none">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-base">help</span>
                                    <span>📖 نحوه کارکرد، شورت‌کدها و ارسال خودکار پیامک هشدار</span>
                                </span>
                                <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="mt-4 pt-3 border-t border-blue-200/60 text-xs text-blue-950 space-y-2 leading-relaxed">
                                <p><strong>نحوه کارکرد:</strong> دکمه‌ها به صورت خودکار در صفحه تک محصول ووکامرس نمایش داده می‌شوند. اگر محصول ناموجود باشد دکمه «موجود شد به من اطلاع بده» و اگر موجود باشد دکمه «ارزان شد به من اطلاع بده» ظاهر می‌شود.</p>
                                <p><strong>استفاده با شورت‌کد دلخواه:</strong> در هر صفحه‌ای از شورت‌کد <code>[palette_price_alert product_id="123"]</code> می‌توانید استفاده فرمایید.</p>
                                <p><strong>ارسال بلادرنگ پیامک:</strong> به محض اینکه موجودی یا قیمت محصول را در پیشخوان تغییر دهید، سیستم به متقاضیان پیامک ارسال می‌کند.</p>
                            </div>
                        </details>
                    </div>
                </div>

                <!-- 5. Affiliate Sub-tab -->
                <div id="sub-feat-affiliate" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">handshake</span>
                                    <span>۵. سیستم بازاریابی و همکاری در فروش (Affiliate)</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">تولید لینک معرف و واریز خودکار کمیسیون به کیف پول معرف پس از تکمیل خرید.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                <span class="text-xs font-bold text-on-surface">فعالسازی همکاری در فروش</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_affiliate]" value="1" <?php checked(!empty($opt['enable_affiliate'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">درصد کمیسیون معرف از مبلغ سفارش (٪)</label>
                                <input type="number" name="settings[affiliate_commission]" value="<?php echo esc_attr($opt['affiliate_commission'] ?? 10); ?>" min="0" max="100" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-sm font-bold text-left font-mono" dir="ltr">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">مدت زمان اعتبار کوکی بازاریاب (روز)</label>
                                <input type="number" name="settings[affiliate_cookie_days]" value="<?php echo esc_attr($opt['affiliate_cookie_days'] ?? 30); ?>" min="1" max="365" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-sm text-left font-mono" dir="ltr">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. RMA Sub-tab -->
                <div id="sub-feat-rma" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">assignment_return</span>
                                    <span>۶. درخواست مرجوعی کالا و گارانتی (RMA)</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">امکان ثبت درخواست بازگشت کالا برای سفارشات تکمیل شده با مهلت روز مشخص.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                <span class="text-xs font-bold text-on-surface">فعالسازی ماژول مرجوعی</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_rma]" value="1" <?php checked(!empty($opt['enable_rma'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">مهلت مجاز ثبت درخواست مرجوعی (روز)</label>
                            <input type="number" name="settings[rma_max_days]" value="<?php echo esc_attr($opt['rma_max_days'] ?? 7); ?>" min="1" max="60" class="w-full max-w-xs bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-mono text-left font-bold" dir="ltr">
                        </div>

                        <!-- Accordion Guide for RMA -->
                        <details class="group bg-blue-50/80 border border-blue-200/80 rounded-2xl p-4 transition-all duration-300">
                            <summary class="flex justify-between items-center cursor-pointer font-bold text-xs text-blue-900 list-none">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-base">help</span>
                                    <span>📖 راهنمای گردش کار مرجوعی کالا در پیشخوان</span>
                                </span>
                                <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="mt-4 pt-3 border-t border-blue-200/60 text-xs text-blue-950 space-y-2 leading-relaxed">
                                <p>• مشتری در تب سفارشات در مهلت روز مشخص شده دکمه «ثبت مرجوعی» را مشاهده می‌نماید.</p>
                                <p>• علت مرجوعی و توضیحات ثبت و پس از تایید توسط ادمین وجه به کیف پول واریز می‌گردد.</p>
                            </div>
                        </details>
                    </div>
                </div>

                <!-- 7. Shahkar Sub-tab -->
                <div id="sub-feat-shahkar" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">verified</span>
                                    <span>۷. احراز هویت شاهکار (تطبیق کدملی و سیم‌کارت)</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">تطبیق هوشمند کدملی با مالکیت خط موبایل ثبت‌نامی جهت امنیت صرافی‌ها و فروشگاه‌ها.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                <span class="text-xs font-bold text-on-surface">فعالسازی استعلام شاهکار</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_shahkar]" value="1" <?php checked(!empty($opt['enable_shahkar'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">کلید وب‌سرویس شاهکار (API Key / Token)</label>
                            <input type="password" name="settings[shahkar_api_key]" value="<?php echo esc_attr($opt['shahkar_api_key'] ?? ''); ?>" placeholder="کلید وب‌سرویس شاهکار" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-mono text-left" dir="ltr">
                        </div>

                        <!-- Accordion Guide for Shahkar -->
                        <details class="group bg-blue-50/80 border border-blue-200/80 rounded-2xl p-4 transition-all duration-300">
                            <summary class="flex justify-between items-center cursor-pointer font-bold text-xs text-blue-900 list-none">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-base">help</span>
                                    <span>📖 راهنمای دریافت وب‌سرویس شاهکار</span>
                                </span>
                                <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="mt-4 pt-3 border-t border-blue-200/60 text-xs text-blue-950 space-y-2 leading-relaxed">
                                <p>وب‌سرویس شاهکار را می‌توانید از ارائه‌دهندگان معتبر احراز هویت (مانند کاوه‌نگار، فینودید یا سازمان فناوری اطلاعات) دریافت و کلید API را درج کنید.</p>
                            </div>
                        </details>
                    </div>
                </div>

                <!-- 8. PWA Sub-tab -->
                <div id="sub-feat-pwa" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">install_mobile</span>
                                    <span>۸. وب‌اپلیکیشن پیش‌رونده (PWA و نصب در موبایل)</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">تبدیل پیشخوان سایت به اپلیکیشن موبایل با قابلیت نصب سریع روی صفحه اصلی اندروید و iOS.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                <span class="text-xs font-bold text-on-surface">فعالسازی قابلیت PWA</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_pwa]" value="1" <?php checked(!empty($opt['enable_pwa'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">نام کوتاه اپلیکیشن (Short Name)</label>
                                <input type="text" name="settings[pwa_short_name]" value="<?php echo esc_attr($opt['pwa_short_name'] ?? get_bloginfo('name')); ?>" class="w-full bg-surface-container-low border-none rounded-2xl p-3.5 text-xs font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant mb-1">رنگ نوار وضعیت (Theme Color)</label>
                                <input type="color" name="settings[pwa_theme_color]" value="<?php echo esc_attr($opt['pwa_theme_color'] ?? '#4c5e8b'); ?>" class="w-12 h-10 p-0 border-none rounded-xl cursor-pointer">
                            </div>
                        </div>

                        <!-- Accordion Guide for PWA -->
                        <details class="group bg-blue-50/80 border border-blue-200/80 rounded-2xl p-4 transition-all duration-300">
                            <summary class="flex justify-between items-center cursor-pointer font-bold text-xs text-blue-900 list-none">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-base">help</span>
                                    <span>📖 راهنمای نصب PWA در گوشی مشتریان</span>
                                </span>
                                <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="mt-4 pt-3 border-t border-blue-200/60 text-xs text-blue-950 space-y-2 leading-relaxed">
                                <p>• در اندروید و گوگل کروم بنر «نصب اپلیکیشن» ظاهر می‌شود.</p>
                                <p>• در آیفون (سافاری)، مشتری با لمس Share > Add to Home Screen می‌تواند اپلیکیشن را اضافه کند.</p>
                            </div>
                        </details>
                    </div>
                </div>

                <!-- 9. Active Sessions Sub-tab -->
                <div id="sub-feat-sessions" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">devices</span>
                                    <span>۹. مدیریت نشست‌های فعال و امنیت ورود (Active Sessions)</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">مشاهده دستگاه‌های متصل، مرورگرها، IP و امکان خروج از سایر دستگاه‌ها توسط کاربر.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                <span class="text-xs font-bold text-on-surface">فعالسازی نشست‌های فعال</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_active_sessions]" value="1" <?php checked(!empty($opt['enable_active_sessions'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 10. License Manager Sub-tab -->
                <div id="sub-feat-licenses" class="sub-content hidden space-y-6">
                    <div class="bg-surface-container-lowest p-7 rounded-[2rem] border border-outline-variant/30 shadow-sm space-y-6">
                        <div class="flex justify-between items-center flex-wrap gap-4 border-b border-outline-variant/20 pb-5">
                            <div>
                                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">vpn_key</span>
                                    <span>۱۰. مدیریت لایسنس‌ها و فایل‌های دانلودی</span>
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">نمایش کلیدهای لایسنس و فایل‌های دانلودی در تب اختصاصی پیشخوان کاربر.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2.5 rounded-2xl">
                                <span class="text-xs font-bold text-on-surface">فعالسازی مدیریت لایسنس</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[enable_license_manager]" value="1" <?php checked(!empty($opt['enable_license_manager'])); ?> class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================== -->
    <!-- STICKY FLOATING BOTTOM SAVE BAR (Soft UI) -->
    <!-- ========================================== -->
    <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-40 w-[95%] max-w-5xl bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border border-outline-variant/40 rounded-3xl p-4 shadow-2xl flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">palette</span>
            </div>
            <div>
                <div class="text-xs font-black text-on-surface">پالت پنل نسخه ۱.۴.۶</div>
                <div class="text-[10px] text-on-surface-variant" id="save-bar-status">آماده ذخیره‌سازی تغییرات</div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="saveSereneSettings()" id="btn-sticky-save" class="bg-primary hover:bg-primary-dim text-white px-8 py-3 rounded-2xl font-bold text-xs shadow-lg shadow-primary/25 transition-all flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-lg">save</span>
                <span>ذخیره کلیه تنظیمات</span>
            </button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- VISUAL ICON PICKER MODAL (Material Symbols) -->
<!-- ========================================== -->
<div id="serene-icon-picker-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest max-w-2xl w-full rounded-3xl p-6 shadow-2xl border border-outline-variant/30 space-y-5">
        <div class="flex justify-between items-center pb-3 border-b border-outline-variant/20">
            <h3 class="font-bold text-base text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">palette</span>
                <span>انتخابگر بصری آیکون (Material Symbols)</span>
            </h3>
            <button type="button" onclick="closeIconPicker()" class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high p-1.5 rounded-xl cursor-pointer">close</button>
        </div>

        <div class="relative">
            <input type="text" id="icon-picker-search" oninput="filterIcons(this.value)" placeholder="جستجوی نام آیکون (مثال: stars, shopping, diamond)..." class="w-full bg-surface-container-low border-none rounded-2xl p-3 pr-10 text-xs">
            <span class="material-symbols-outlined absolute right-3 text-on-surface-variant text-base">search</span>
        </div>

        <div id="icon-picker-grid" class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2 max-h-80 overflow-y-auto p-2 border border-outline-variant/20 rounded-2xl bg-surface-container-low/30">
            <?php
            $material_icons = [
                'dashboard', 'shopping_bag', 'account_balance_wallet', 'chat', 'casino', 'card_giftcard',
                'handshake', 'cloud_download', 'person', 'location_on', 'storefront', 'shopping_cart',
                'local_shipping', 'receipt', 'stars', 'workspace_premium', 'military_tech', 'diamond',
                'notifications', 'badge', 'settings', 'lock', 'key', 'fingerprint', 'verified',
                'install_mobile', 'devices', 'rate_review', 'forum', 'support_agent', 'help', 'info',
                'favorite', 'thumb_up', 'share', 'bookmark', 'search', 'sell', 'percent', 'savings',
                'payments', 'confirmation_number', 'loyalty', 'inventory', 'assignment_return', 'campaign'
            ];
            foreach ($material_icons as $ico):
            ?>
            <button type="button" onclick="selectPickedIcon('<?php echo esc_js($ico); ?>')" class="icon-pick-btn p-2.5 rounded-xl hover:bg-primary hover:text-white flex flex-col items-center justify-center gap-1 transition-all cursor-pointer bg-white border border-outline-variant/20 group" title="<?php echo esc_attr($ico); ?>">
                <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform"><?php echo esc_html($ico); ?></span>
                <span class="text-[9px] truncate w-full text-center font-mono opacity-70 group-hover:opacity-100"><?php echo esc_html($ico); ?></span>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
const config = {
    ajax_url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
    nonce: '<?php echo wp_create_nonce('serene_panel_nonce'); ?>'
};

// URL Hash Persistence
function updateUrlHash(catId, subId) {
    let hash = '#' + catId;
    if (subId) hash += '&sub=' + subId;
    history.replaceState(null, null, hash);
}

function restoreTabsFromHash() {
    const hash = window.location.hash.substring(1);
    if (!hash) return;

    const parts = hash.split('&sub=');
    const catId = parts[0];
    const subId = parts[1];

    if (catId) {
        const catBtn = document.querySelector(`button[onclick*="'${catId}'"]`);
        if (catBtn) switchMainCat(catId, catBtn, false);
    }
    if (subId) {
        const subBtn = document.querySelector(`button[onclick*="'${subId}'"]`);
        if (subBtn) switchSubTab(subId, subBtn, false);
    }
}

function showAdminNotification(message, type = 'success') {
    const toast = document.getElementById('admin-notif-toast');
    if (!toast) return;

    toast.className = 'fixed bottom-24 left-6 z-50 p-4 rounded-2xl shadow-2xl text-xs font-bold transition-all duration-300 max-w-sm flex items-center gap-3 ' + 
        (type === 'success' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white');
    toast.innerHTML = `<span class="material-symbols-outlined text-lg">${type === 'success' ? 'check_circle' : 'error'}</span><span>${message}</span>`;
    toast.classList.remove('hidden');

    const statusText = document.getElementById('save-bar-status');
    if (statusText) {
        statusText.innerText = (type === 'success') ? 'آخرین تغییرات با موفقیت ذخیره شد.' : 'خطا در ذخیره‌سازی!';
    }

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 4000);
}

function switchMainCat(catId, btn, updateHash = true) {
    document.querySelectorAll('.main-cat-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.main-cat-tab').forEach(b => {
        b.className = 'main-cat-tab px-5 py-3 rounded-2xl font-black text-xs flex items-center gap-2 bg-transparent text-on-surface-variant hover:bg-surface-container-high transition-all cursor-pointer';
    });

    const activeCat = document.getElementById(catId);
    if (activeCat) activeCat.classList.remove('hidden');

    btn.className = 'main-cat-tab active px-5 py-3 rounded-2xl font-black text-xs flex items-center gap-2 bg-primary text-white shadow-md transition-all cursor-pointer';

    if (updateHash) {
        const activeSubBtn = activeCat ? activeCat.querySelector('.sub-tab.active') : null;
        let subId = '';
        if (activeSubBtn) {
            const m = activeSubBtn.getAttribute('onclick').match(/'([^']+)'/);
            if (m) subId = m[1];
        }
        updateUrlHash(catId, subId);
    }
}

function switchSubTab(subId, btn, updateHash = true) {
    const parentCat = btn.closest('.main-cat-content');
    if (!parentCat) return;

    parentCat.querySelectorAll('.sub-content').forEach(el => el.classList.add('hidden'));
    parentCat.querySelectorAll('.sub-tab').forEach(b => {
        b.className = 'sub-tab px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface transition-all';
    });

    const activeSub = document.getElementById(subId);
    if (activeSub) activeSub.classList.remove('hidden');

    btn.className = 'sub-tab active px-4 py-2 rounded-xl text-xs font-bold bg-white text-primary shadow-sm transition-all';

    if (updateHash) {
        updateUrlHash(parentCat.id, subId);
    }
}

function selectLayoutCard(val, cardEl) {
    document.getElementById('input-login-theme-style').value = val;
    document.querySelectorAll('#login-layout-cards .layout-card').forEach(c => {
        c.className = 'layout-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between border-outline-variant/30 hover:border-primary/40';
        const b = c.querySelector('.layout-check-badge');
        if (b) b.classList.add('hidden');
    });

    cardEl.className = 'layout-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between border-primary bg-primary/5 shadow-md ring-2 ring-primary/20';
    const activeBadge = cardEl.querySelector('.layout-check-badge');
    if (activeBadge) activeBadge.classList.remove('hidden');
}

function selectSkinCard(val, cardEl) {
    document.getElementById('input-admin-dashboard-theme').value = val;
    document.querySelectorAll('#admin-skin-cards .skin-card').forEach(c => {
        c.className = 'skin-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between border-outline-variant/30 hover:border-primary/40';
        const b = c.querySelector('.skin-check-badge');
        if (b) b.classList.add('hidden');
    });

    cardEl.className = 'skin-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between border-primary bg-primary/5 shadow-md ring-2 ring-primary/20';
    const activeBadge = cardEl.querySelector('.skin-check-badge');
    if (activeBadge) activeBadge.classList.remove('hidden');
}

function selectInputStyle(val, cardEl) {
    document.getElementById('input-field-style').value = val;
    document.querySelectorAll('#input-style-cards .input-style-card').forEach(c => {
        c.className = 'input-style-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between border-outline-variant/30 bg-white hover:border-primary/40';
        const b = c.querySelector('.input-check-badge');
        if (b) b.classList.add('hidden');
    });

    cardEl.className = 'input-style-card cursor-pointer border-2 rounded-2xl p-4 transition-all duration-200 relative flex flex-col justify-between border-primary bg-white shadow-md ring-2 ring-primary/20';
    const activeBadge = cardEl.querySelector('.input-check-badge');
    if (activeBadge) activeBadge.classList.remove('hidden');
}

// Media Library Uploader
function openMediaUploader(inputId, previewId) {
    const customUploader = wp.media({
        title: 'انتخاب یا بارگذاری تصویر',
        button: { text: 'استفاده از این تصویر' },
        multiple: false
    });

    customUploader.on('select', function() {
        const attachment = customUploader.state().get('selection').first().toJSON();
        document.getElementById(inputId).value = attachment.url;
        if (previewId) {
            const previewEl = document.getElementById(previewId);
            if (previewEl) {
                previewEl.innerHTML = `<img src="${attachment.url}" class="max-h-full object-contain">`;
                previewEl.classList.remove('hidden');
            }
        }
    });

    customUploader.open();
}

function saveSereneSettings() {
    const stickyBtn = document.getElementById('btn-sticky-save');
    const triggers = document.querySelectorAll('.btn-save-trigger');
    
    if (stickyBtn) stickyBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-lg">sync</span><span>در حال ذخیره...</span>';
    triggers.forEach(b => b.innerHTML = '<span class="material-symbols-outlined animate-spin text-lg">sync</span><span>در حال ذخیره...</span>');

    const form = document.getElementById('serene-settings-form');
    const formData = new FormData(form);
    formData.append('action', 'serene_admin_save_settings');
    formData.append('nonce', config.nonce);

    fetch(config.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (stickyBtn) stickyBtn.innerHTML = '<span class="material-symbols-outlined text-lg">save</span><span>ذخیره کلیه تنظیمات</span>';
        triggers.forEach(b => b.innerHTML = '<span class="material-symbols-outlined text-lg">save</span><span>ذخیره تنظیمات</span>');
        
        if (data.success) {
            showAdminNotification(data.data.message || 'کلیه تنظیمات با موفقیت ذخیره شد.', 'success');
        } else {
            showAdminNotification(data.data.message || 'خطا در ذخیره‌سازی تنظیمات.', 'error');
        }
    })
    .catch(() => {
        if (stickyBtn) stickyBtn.innerHTML = '<span class="material-symbols-outlined text-lg">save</span><span>ذخیره کلیه تنظیمات</span>';
        triggers.forEach(b => b.innerHTML = '<span class="material-symbols-outlined text-lg">save</span><span>ذخیره تنظیمات</span>');
        showAdminNotification('خطا در برقراری ارتباط با سرور.', 'error');
    });
}

function toggleSmsFields() {
    const select = document.getElementById('sms-provider-select');
    const val = select ? select.value : 'smsir';
    document.querySelectorAll('.sms-fields').forEach(el => el.classList.add('hidden'));
    const target = document.getElementById('provider-' + val);
    if (target) target.classList.remove('hidden');
}

function sendTestSMS() {
    const phone = document.getElementById('test-sms-phone').value;
    const resEl = document.getElementById('test-sms-result');
    if (!phone) return showAdminNotification('لطفاً شماره موبایل را وارد کنید.', 'error');

    const formData = new FormData();
    formData.append('action', 'serene_admin_test_sms');
    formData.append('phone', phone);
    formData.append('nonce', config.nonce);

    resEl.className = 'p-3 rounded-xl text-xs font-medium bg-blue-50 text-blue-700 w-full';
    resEl.innerText = 'در حال ارسال پیامک تستی...';
    resEl.classList.remove('hidden');

    fetch(config.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resEl.className = 'p-3 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 w-full';
            resEl.innerText = data.data.message;
        } else {
            resEl.className = 'p-3 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 w-full';
            resEl.innerText = data.data.message || 'خطا در ارسال پیامک.';
        }
    })
    .catch(() => {
        resEl.className = 'p-3 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 w-full';
        resEl.innerText = 'خطای ارتباط با سرور.';
    });
}

function testBankSmsWebhook() {
    const sms = document.getElementById('test-webhook-sms').value.trim();
    const resEl = document.getElementById('test-webhook-result');
    const btn = document.getElementById('btn-test-webhook');
    const secretInput = document.querySelector('input[name="settings[c2c_webhook_secret]"]');
    const secret = secretInput ? secretInput.value.trim() : 'palette_sec_key_123';

    if (!sms) {
        return showAdminNotification('لطفاً ابتدا یک متن پیامک بانکی وارد کنید.', 'error');
    }

    if (btn) btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-base">sync</span><span>در حال ارسال...</span>';
    resEl.className = 'p-4 rounded-xl text-xs font-mono bg-blue-50 text-blue-900 border border-blue-200 block';
    resEl.innerText = 'در حال ارسال درخواست HTTP POST به آدرس وب‌هوک...';

    const webhookUrl = '<?php echo esc_url_raw(rest_url('palette/v1/bank-sms-webhook')); ?>';

    fetch(webhookUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ secret: secret, sms: sms })
    })
    .then(r => r.json().then(data => ({ status: r.status, data: data })))
    .then(res => {
        if (btn) btn.innerHTML = '<span class="material-symbols-outlined text-base">send</span><span>ارسال درخواست تستی به وب‌هوک</span>';
        if (res.status === 200 && res.data.status === 'success') {
            resEl.className = 'p-4 rounded-xl text-xs font-mono bg-emerald-50 text-emerald-900 border border-emerald-300 block space-y-2';
            resEl.innerHTML = `
                <div class="font-bold text-sm text-emerald-700">✓ وب‌هوک با موفقیت پاسخ داد (کد وضعیت: ${res.status})</div>
                <div><strong>بانک شناسایی‌شده:</strong> ${res.data.parsed.bank}</div>
                <div><strong>مبلغ استخراج‌شده:</strong> ${Number(res.data.parsed.amount).toLocaleString('fa-IR')} تومان</div>
                <div><strong>کد رهگیری / ارجاع:</strong> ${res.data.parsed.tracking_code || 'استخراج نشد'}</div>
                <div><strong>نتیجه تطبیق با سفارش:</strong> ${res.data.matched ? ('سفارش شماره #' + res.data.matched.order_id + ' تایید شد') : 'سفارش در انتظار منطبقی در سیستم وجود نداشت (آماده تطبیق)'}</div>
                <pre class="mt-2 p-2 bg-slate-900 text-emerald-300 rounded text-[11px] overflow-x-auto" dir="ltr">${JSON.stringify(res.data, null, 2)}</pre>
            `;
        } else {
            resEl.className = 'p-4 rounded-xl text-xs font-mono bg-amber-50 text-amber-900 border border-amber-300 block space-y-2';
            resEl.innerHTML = `
                <div class="font-bold text-sm text-amber-800">پاسخ وب‌هوک: ${res.data.message || 'نامشخص'} (کد: ${res.status})</div>
                <pre class="mt-2 p-2 bg-slate-900 text-amber-300 rounded text-[11px] overflow-x-auto" dir="ltr">${JSON.stringify(res.data, null, 2)}</pre>
            `;
        }
    })
    .catch(err => {
        if (btn) btn.innerHTML = '<span class="material-symbols-outlined text-base">send</span><span>ارسال درخواست تستی به وب‌هوک</span>';
        resEl.className = 'p-4 rounded-xl text-xs font-mono bg-rose-50 text-rose-900 border border-rose-300 block';
        resEl.innerText = 'خطا در برقراری ارتباط با وب‌هوک: ' + err.message;
    });
}

function learnBankPattern() {
    const bankName = document.getElementById('learn-bank-name').value.trim();
    const unit = document.getElementById('learn-currency-unit').value;
    const sampleSms = document.getElementById('learn-sample-sms').value.trim();
    const btn = document.getElementById('btn-learn-pattern');
    const feedback = document.getElementById('learn-pattern-feedback');

    if (!bankName) return showAdminNotification('لطفاً نام بانک را وارد کنید.', 'error');
    if (!sampleSms) return showAdminNotification('لطفاً نمونه پیامک بانک را وارد کنید.', 'error');

    if (btn) btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-base">sync</span><span>در حال یادگیری...</span>';
    feedback.className = 'p-3.5 rounded-xl text-xs font-mono bg-blue-50 text-blue-900 border border-blue-200 block';
    feedback.innerText = 'در حال تجزیه و استخراج ساختار پیامک توسط یادگیرنده هوشمند...';

    const fd = new FormData();
    fd.append('action', 'serene_admin_learn_bank_pattern');
    fd.append('bank_name', bankName);
    fd.append('currency_unit', unit);
    fd.append('sample_sms', sampleSms);
    fd.append('nonce', config.nonce);

    fetch(config.ajax_url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (btn) btn.innerHTML = '<span class="material-symbols-outlined text-base">auto_awesome</span><span>یادگیری و ثبت خودکار الگو</span>';
        if (res.success) {
            feedback.className = 'p-3.5 rounded-xl text-xs font-mono bg-emerald-50 text-emerald-900 border border-emerald-300 block space-y-1';
            feedback.innerHTML = `
                <div class="font-bold text-emerald-800">✓ ${res.data.message}</div>
                <div><strong>ریجکس استخراج‌شده:</strong> <code class="bg-white px-2 py-0.5 rounded border border-emerald-200" dir="ltr">${res.data.pattern.regex}</code></div>
            `;
            showAdminNotification('الگوی بانک جدید با موفقیت یاد گرفته و ذخیره شد.', 'success');
            
            const noMsg = document.getElementById('no-custom-patterns-msg');
            if (noMsg) noMsg.remove();
            
            const list = document.getElementById('custom-patterns-list');
            if (list) {
                const row = document.createElement('div');
                row.id = 'pat-row-' + res.data.pattern.id;
                row.className = 'p-3 bg-surface-container-low rounded-xl border border-outline-variant/20 flex justify-between items-center text-xs';
                row.innerHTML = `
                    <div class="space-y-1">
                        <span class="font-bold text-primary">${res.data.pattern.bank_name}</span>
                        <span class="text-[10px] text-on-surface-variant bg-white px-2 py-0.5 rounded-md border border-outline-variant/20 mr-2">واحد: ${res.data.pattern.unit === 'toman' ? 'تومان' : 'ریال'}</span>
                        <div class="font-mono text-[10px] text-slate-600 truncate max-w-md" dir="ltr">${res.data.pattern.regex}</div>
                    </div>
                    <button type="button" onclick="deleteBankPattern('${res.data.pattern.id}')" class="text-rose-600 hover:bg-rose-50 p-1.5 rounded-lg text-xs font-bold flex items-center gap-1 cursor-pointer">
                        <span class="material-symbols-outlined text-sm">delete</span>
                        <span>حذف</span>
                    </button>
                `;
                list.prepend(row);
            }
            document.getElementById('learn-sample-sms').value = '';
        } else {
            feedback.className = 'p-3.5 rounded-xl text-xs font-mono bg-rose-50 text-rose-900 border border-rose-300 block';
            feedback.innerText = 'خطا: ' + (res.data?.message || 'الگو استخراج نشد.');
        }
    })
    .catch(() => {
        if (btn) btn.innerHTML = '<span class="material-symbols-outlined text-base">auto_awesome</span><span>یادگیری و ثبت خودکار الگو</span>';
        feedback.className = 'p-3.5 rounded-xl text-xs font-mono bg-rose-50 text-rose-900 border border-rose-300 block';
        feedback.innerText = 'خطا در ارتباط با سرور.';
    });
}

function deleteBankPattern(patternId) {
    if (!confirm('آیا از حذف این الگوی پیامک اطمینان دارید؟')) return;

    const fd = new FormData();
    fd.append('action', 'serene_admin_delete_bank_pattern');
    fd.append('pattern_id', patternId);
    fd.append('nonce', config.nonce);

    fetch(config.ajax_url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('pat-row-' + patternId);
            if (row) row.remove();
            showAdminNotification('الگو با موفقیت حذف شد.', 'success');
        } else {
            showAdminNotification(res.data?.message || 'خطا در حذف الگو.', 'error');
        }
    });
}

let userSearchTimeout = null;
function searchUsersForWallet(query) {
    clearTimeout(userSearchTimeout);
    const resultsContainer = document.getElementById('admin-wallet-user-results');
    if (!query || query.length < 2) {
        resultsContainer.classList.add('hidden');
        resultsContainer.innerHTML = '';
        return;
    }

    userSearchTimeout = setTimeout(() => {
        const formData = new FormData();
        formData.append('action', 'serene_admin_search_users');
        formData.append('term', query);
        formData.append('nonce', config.nonce);

        fetch(config.ajax_url, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (!data.success || !data.data.users || !data.data.users.length) {
                resultsContainer.innerHTML = '<div class="p-3 text-xs text-on-surface-variant text-center">کاربری یافت نشد.</div>';
                resultsContainer.classList.remove('hidden');
                return;
            }

            let html = '';
            data.data.users.forEach(u => {
                const safeName = (u.name || '').replace(/'/g, "\\'");
                const safeInfo = `${u.email} | ${u.phone}`.replace(/'/g, "\\'");
                html += `
                <div onclick="selectUserForWallet(${u.id}, '${safeName}', '${safeInfo}', '${u.balance_formatted}', '${u.avatar}')" class="p-3 hover:bg-surface-container cursor-pointer flex items-center justify-between transition-colors">
                    <div class="flex items-center gap-3">
                        <img src="${u.avatar}" class="w-8 h-8 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-xs text-on-surface">${u.name} (@${u.login})</div>
                            <div class="text-[10px] text-on-surface-variant">${u.email} • ${u.phone}</div>
                        </div>
                    </div>
                    <div class="text-xs font-bold text-primary font-mono">${u.balance_formatted} تومان</div>
                </div>`;
            });

            resultsContainer.innerHTML = html;
            resultsContainer.classList.remove('hidden');
        });
    }, 300);
}

function selectUserForWallet(id, name, info, balance, avatar) {
    document.getElementById('admin-wallet-selected-uid').value = id;
    document.getElementById('selected-user-name').innerText = name;
    document.getElementById('selected-user-info').innerText = info;
    document.getElementById('selected-user-balance').innerText = balance;
    document.getElementById('selected-user-avatar').src = avatar;

    document.getElementById('admin-wallet-user-results').classList.add('hidden');
    document.getElementById('admin-wallet-selected-card').classList.remove('hidden');
    document.getElementById('admin-wallet-form-box').classList.remove('hidden');
}

function submitAdminWalletAdjustment() {
    const userId = document.getElementById('admin-wallet-selected-uid').value;
    const actionType = document.getElementById('admin-wallet-action-type').value;
    const amount = document.getElementById('admin-wallet-amount').value;
    const reason = document.getElementById('admin-wallet-reason').value;
    const btn = document.getElementById('btn-adjust-wallet');

    if (!userId) return showAdminNotification('لطفاً ابتدا یک کاربر را انتخاب کنید.', 'error');
    if (!amount || parseFloat(amount) <= 0) return showAdminNotification('لطفاً مبلغ معتبر وارد نمایید.', 'error');

    if (btn) btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-base">sync</span><span>در حال ثبت...</span>';

    const formData = new FormData();
    formData.append('action', 'serene_admin_adjust_wallet');
    formData.append('user_id', userId);
    formData.append('action_type', actionType);
    formData.append('amount', amount);
    formData.append('reason', reason);
    formData.append('nonce', config.nonce);

    fetch(config.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (btn) btn.innerHTML = '<span class="material-symbols-outlined text-base">check_circle</span><span>ثبت و اعمال تغییر موجودی</span>';
        if (data.success) {
            document.getElementById('selected-user-balance').innerText = data.data.balance_formatted;
            document.getElementById('admin-wallet-amount').value = '';
            document.getElementById('admin-wallet-reason').value = '';
            showAdminNotification(data.data.message, 'success');
        } else {
            showAdminNotification(data.data.message || 'خطا در اعمال تغییر موجودی.', 'error');
        }
    })
    .catch(() => {
        if (btn) btn.innerHTML = '<span class="material-symbols-outlined text-base">check_circle</span><span>ثبت و اعمال تغییر موجودی</span>';
        showAdminNotification('خطای ارتباط با سرور.', 'error');
    });
}

function submitBulkWalletAdjustment() {
    const role = document.getElementById('bulk-wallet-role').value;
    const actionType = document.getElementById('bulk-wallet-action').value;
    const amount = document.getElementById('bulk-wallet-amount').value;
    const reason = document.getElementById('bulk-wallet-reason').value;
    const btn = document.getElementById('btn-bulk-wallet');

    if (!amount || parseFloat(amount) <= 0) return showAdminNotification('لطفاً مبلغ معتبر وارد کنید.', 'error');
    if (!confirm('آیا از اعمال این عملیات مالی گروهی برای کاربران اطمینان دارید؟')) return;

    if (btn) btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-base">sync</span><span>در حال پردازش گروهی...</span>';

    const formData = new FormData();
    formData.append('action', 'serene_admin_bulk_adjust_wallet');
    formData.append('target_role', role);
    formData.append('action_type', actionType);
    formData.append('amount', amount);
    formData.append('reason', reason);
    formData.append('nonce', config.nonce);

    fetch(config.ajax_url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (btn) btn.innerHTML = '<span class="material-symbols-outlined text-lg">bolt</span><span>شروع و اعمال عملیات گروهی</span>';
        if (data.success) {
            showAdminNotification(data.data.message, 'success');
        } else {
            showAdminNotification(data.data.message || 'خطا در عملیات گروهی.', 'error');
        }
    })
    .catch(() => {
        if (btn) btn.innerHTML = '<span class="material-symbols-outlined text-lg">bolt</span><span>شروع و اعمال عملیات گروهی</span>';
        showAdminNotification('خطای ارتباط با سرور.', 'error');
    });
}

function calculateWheelProbabilities() {
    const rows = document.querySelectorAll('#wheel-slices-table tbody tr');
    let totalWeight = 0;
    const weights = [];

    rows.forEach(r => {
        const inp = r.querySelector('.slice-weight-input');
        const w = inp ? (parseFloat(inp.value) || 0) : 0;
        weights.push(w);
        totalWeight += w;
    });

    if (totalWeight <= 0) totalWeight = 1;

    rows.forEach((r, idx) => {
        const display = r.querySelector('.slice-prob-display');
        if (display) {
            const percent = ((weights[idx] / totalWeight) * 100).toFixed(1);
            display.innerText = percent + ' %';
        }
    });
}

function addNewWheelSlice() {
    const tbody = document.querySelector('#wheel-slices-table tbody');
    const idx = tbody.rows.length;
    const row = tbody.insertRow();
    row.className = 'slice-row';
    row.innerHTML = `
        <td class="p-2">
            <input type="text" name="wheel_slices[${idx}][label]" value="جایزه جدید" class="w-full bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
        </td>
        <td class="p-2">
            <select name="wheel_slices[${idx}][type]" class="bg-surface-container-low border-none rounded-xl p-2 text-xs">
                <option value="coupon">کد تخفیف اختصاصی</option>
                <option value="wallet">شارژ کیف پول (تومان)</option>
                <option value="empty">پوچ / دوباره بچرخ</option>
            </select>
        </td>
        <td class="p-2">
            <input type="text" name="wheel_slices[${idx}][value]" value="10%" placeholder="مقدار یا درصد" class="w-28 bg-surface-container-low border-none rounded-xl p-2.5 text-xs text-left font-mono" dir="ltr">
        </td>
        <td class="p-2 text-center">
            <input type="number" name="wheel_slices[${idx}][weight]" value="10" min="1" max="200" oninput="calculateWheelProbabilities()" class="w-16 bg-surface-container-low border-none rounded-xl p-2 text-xs text-center slice-weight-input font-mono" dir="ltr">
        </td>
        <td class="p-2 text-center font-bold font-mono text-primary slice-prob-display">--%</td>
        <td class="p-2 text-center">
            <input type="color" name="wheel_slices[${idx}][color]" value="#4c5e8b" class="w-9 h-8 p-0 border-none rounded cursor-pointer">
        </td>
        <td class="p-2 text-center">
            <input type="color" name="wheel_slices[${idx}][text]" value="#ffffff" class="w-9 h-8 p-0 border-none rounded cursor-pointer">
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف اسلایس">
                <span class="material-symbols-outlined text-lg leading-none">delete</span>
            </button>
        </td>`;
    calculateWheelProbabilities();
}

// Loyalty Tiers Dynamic Row Manager
function addNewLoyaltyTierRow() {
    const tbody = document.querySelector('#loyalty-tiers-table tbody');
    const idx = 'tier_' + Date.now();
    const row = tbody.insertRow();
    row.className = 'tier-row';
    row.setAttribute('data-id', idx);
    row.innerHTML = `
        <td class="p-2">
            <input type="text" value="سطح جدید" class="tier-name-input w-28 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
        </td>
        <td class="p-2">
            <div class="flex items-center gap-1">
                <input type="hidden" value="stars" class="tier-icon-input row-icon-input">
                <button type="button" onclick="openIconPicker(this)" class="bg-surface-container-low hover:bg-surface-container px-2 py-1.5 rounded-xl border border-outline-variant/30 flex items-center gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-primary text-base row-icon-preview">stars</span>
                </button>
            </div>
        </td>
        <td class="p-2">
            <input type="color" value="#cd7f32" class="tier-color-input w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
        </td>
        <td class="p-2">
            <input type="number" value="10000000" class="tier-spent-input w-28 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-left" dir="ltr">
        </td>
        <td class="p-2">
            <select class="tier-lookback-input bg-surface-container-low border-none rounded-xl p-2 text-xs font-bold">
                <option value="0">کل تاریخچه</option>
                <option value="1">۱ ماه اخیر</option>
                <option value="3">۳ ماه اخیر</option>
                <option value="6" selected>۶ ماه اخیر</option>
                <option value="12">۱۲ ماه اخیر</option>
            </select>
        </td>
        <td class="p-2">
            <input type="number" value="3" class="tier-orders-input w-16 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-center" dir="ltr">
        </td>
        <td class="p-2">
            <input type="number" value="5" class="tier-cashback-input w-16 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-center font-bold text-emerald-600" dir="ltr">
        </td>
        <td class="p-2">
            <input type="number" value="5" class="tier-discount-input w-16 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-center font-bold text-primary" dir="ltr">
        </td>
        <td class="p-2">
            <input type="text" value="تخفیف ویژه و پاداش نقدی" class="tier-perks-input w-44 bg-surface-container-low border-none rounded-xl p-2.5 text-xs">
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف سطح">
                <span class="material-symbols-outlined text-lg leading-none">delete</span>
            </button>
        </td>`;
}

function saveLoyaltyTiersList() {
    const rows = document.querySelectorAll('#loyalty-tiers-table tbody tr');
    const tiers = {};
    rows.forEach(r => {
        const id = r.getAttribute('data-id') || ('tier_' + Math.random().toString(36).substr(2, 6));
        const name = r.querySelector('.tier-name-input').value.trim();
        const icon = r.querySelector('.tier-icon-input').value.trim() || 'stars';
        const color = r.querySelector('.tier-color-input').value;
        const min_spent = parseFloat(r.querySelector('.tier-spent-input').value) || 0;
        const lookback_months = parseInt(r.querySelector('.tier-lookback-input').value) || 0;
        const min_orders = parseInt(r.querySelector('.tier-orders-input').value) || 0;
        const cashback_percent = parseFloat(r.querySelector('.tier-cashback-input').value) || 0;
        const discount = parseFloat(r.querySelector('.tier-discount-input').value) || 0;
        const perks = r.querySelector('.tier-perks-input').value.trim();

        if (name) {
            tiers[id] = { id, name, icon, color, min_spent, lookback_months, min_orders, cashback_percent, discount, perks };
        }
    });

    const fd = new FormData();
    fd.append('action', 'serene_admin_save_loyalty_tiers');
    fd.append('tiers', JSON.stringify(tiers));
    fd.append('nonce', config.nonce);

    fetch(config.ajax_url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showAdminNotification(d.data.message, 'success');
        } else {
            showAdminNotification(d.data.message || 'خطا در ذخیره سطوح باشگاه مشتریان.', 'error');
        }
    });
}

let activeIconRowTrigger = null;
function openIconPicker(btn) {
    activeIconRowTrigger = btn;
    document.getElementById('serene-icon-picker-modal').classList.remove('hidden');
    document.getElementById('icon-picker-search').value = '';
    filterIcons('');
}

function closeIconPicker() {
    document.getElementById('serene-icon-picker-modal').classList.add('hidden');
}

function filterIcons(q) {
    const term = (q || '').toLowerCase().trim();
    document.querySelectorAll('.icon-pick-btn').forEach(btn => {
        const title = btn.getAttribute('title').toLowerCase();
        if (!term || title.includes(term)) {
            btn.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
        }
    });
}

function selectPickedIcon(iconName) {
    if (activeIconRowTrigger) {
        const container = activeIconRowTrigger.closest('td') || activeIconRowTrigger.parentElement;
        const input = container.querySelector('.row-icon-input');
        const preview = container.querySelector('.row-icon-preview');

        if (input) input.value = iconName;
        if (preview) preview.innerText = iconName;
    }
    closeIconPicker();
}

function addNewMenuItem() {
    const tbody = document.querySelector('#menu-builder-table tbody');
    const idx = 'custom_' + Date.now();
    const row = tbody.insertRow();
    row.className = 'menu-row';
    row.innerHTML = `
        <td class="p-2 text-center">
            <input type="checkbox" name="menu_items[${idx}][enabled]" value="1" checked class="rounded text-primary">
        </td>
        <td class="p-2">
            <input type="text" name="menu_items[${idx}][id]" value="${idx}" class="w-24 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono" readonly>
        </td>
        <td class="p-2">
            <input type="text" name="menu_items[${idx}][title]" value="لینک جدید" class="w-32 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
        </td>
        <td class="p-2">
            <div class="flex items-center gap-1.5">
                <input type="hidden" name="menu_items[${idx}][icon]" value="star" class="row-icon-input">
                <button type="button" onclick="openIconPicker(this)" class="bg-surface-container-low hover:bg-surface-container px-2.5 py-1.5 rounded-xl border border-outline-variant/30 flex items-center gap-1.5 cursor-pointer text-xs font-bold">
                    <span class="material-symbols-outlined text-primary text-base row-icon-preview">star</span>
                    <span class="text-[10px] text-on-surface-variant font-mono">star</span>
                </button>
            </div>
        </td>
        <td class="p-2">
            <select name="menu_items[${idx}][type]" class="bg-surface-container-low border-none rounded-xl p-2 text-xs">
                <option value="custom_link">لینک دلخواه</option>
                <option value="endpoint">تب داخلی</option>
                <option value="shortcode">شورت‌کد</option>
            </select>
        </td>
        <td class="p-2">
            <input type="text" name="menu_items[${idx}][target]" value="https://" class="w-32 bg-surface-container-low border-none rounded-xl p-2.5 text-xs text-left" dir="ltr">
        </td>
        <td class="p-2">
            <input type="text" name="menu_items[${idx}][badge]" value="" class="w-16 bg-surface-container-low border-none rounded-xl p-2.5 text-xs">
        </td>
        <td class="p-2">
            <select name="menu_items[${idx}][roles]" class="bg-surface-container-low border-none rounded-xl p-2 text-xs">
                <option value="all">همه کاربران</option>
                <option value="customer">فقط مشتریان</option>
                <option value="administrator">فقط مدیران</option>
            </select>
        </td>
        <td class="p-2 text-center">
            <input type="number" name="menu_items[${idx}][order]" value="${tbody.rows.length + 1}" class="w-12 bg-surface-container-low border-none rounded-xl p-2 text-xs text-center" dir="ltr">
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف آیتم">
                <span class="material-symbols-outlined text-lg leading-none">delete</span>
            </button>
        </td>`;
}

function addNewFormField() {
    const tbody = document.querySelector('#form-builder-table tbody');
    const idx = 'field_' + Date.now();
    const row = tbody.insertRow();
    row.className = 'form-row hover:bg-surface-container-low/40 transition-colors';
    row.innerHTML = `
        <td class="p-3">
            <input type="text" name="custom_fields[${idx}][key]" value="${idx}" placeholder="مثال: custom_key" class="w-full min-w-[130px] bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-left" dir="ltr" required>
        </td>
        <td class="p-3">
            <input type="text" name="custom_fields[${idx}][label]" value="عنوان فیلد جدید" placeholder="عنوان نمایشی..." class="w-full min-w-[140px] bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
        </td>
        <td class="p-3">
            <select name="custom_fields[${idx}][type]" class="w-full min-w-[140px] bg-surface-container-low border-none rounded-xl p-2 text-xs">
                <option value="text">متن کوتاه (Text)</option>
                <option value="number">عددی (Number)</option>
                <option value="date">تاریخ (Date)</option>
                <option value="textarea">متن بلند (Textarea)</option>
            </select>
        </td>
        <td class="p-3 text-center">
            <label class="relative inline-flex items-center justify-center cursor-pointer">
                <input type="checkbox" name="custom_fields[${idx}][required]" value="1" class="sr-only peer">
                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all after:shadow-xs peer-checked:bg-primary"></div>
            </label>
        </td>
        <td class="p-3 text-center">
            <label class="relative inline-flex items-center justify-center cursor-pointer">
                <input type="checkbox" name="custom_fields[${idx}][show_in_register]" value="1" checked class="sr-only peer">
                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all after:shadow-xs peer-checked:bg-primary"></div>
            </label>
        </td>
        <td class="p-3 text-center">
            <label class="relative inline-flex items-center justify-center cursor-pointer">
                <input type="checkbox" name="custom_fields[${idx}][show_in_checkout]" value="1" class="sr-only peer">
                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all after:shadow-xs peer-checked:bg-primary"></div>
            </label>
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف فیلد">
                <span class="material-symbols-outlined text-lg leading-none">delete</span>
            </button>
        </td>`;
}

function addNewChatChannelRow() {
    const tbody = document.querySelector('#chat-channels-table tbody');
    const idx = 'ch_' + Date.now();
    const row = tbody.insertRow();
    row.className = 'chat-ch-row';
    row.innerHTML = `
        <td class="p-2 text-center">
            <input type="checkbox" name="chat_channels[${idx}][enabled]" value="1" checked class="rounded text-primary">
        </td>
        <td class="p-2">
            <input type="text" name="chat_channels[${idx}][title]" value="پشتیبانی آنلاین" class="w-36 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
        </td>
        <td class="p-2">
            <input type="text" name="chat_channels[${idx}][subtitle]" value="پاسخگویی سریع" placeholder="پاسخگویی سریع..." class="w-36 bg-surface-container-low border-none rounded-xl p-2.5 text-xs">
        </td>
        <td class="p-2">
            <select name="chat_channels[${idx}][type]" class="bg-surface-container-low border-none rounded-xl p-2 text-xs font-bold">
                <option value="ticket">🎫 تیکت پشتیبانی</option>
                <option value="whatsapp">💬 واتساپ</option>
                <option value="telegram">✈️ تلگرام</option>
                <option value="phone">📞 تماس مستقیم</option>
                <option value="sms">✉️ پیامک</option>
                <option value="custom">🔗 لینک دلخواه (ایتا، روبیکا...)</option>
            </select>
        </td>
        <td class="p-2">
            <input type="text" name="chat_channels[${idx}][url]" value="" placeholder="https://... یا شماره" class="w-44 bg-surface-container-low border-none rounded-xl p-2.5 text-xs text-left font-mono" dir="ltr">
        </td>
        <td class="p-2">
            <div class="flex items-center gap-1">
                <input type="hidden" name="chat_channels[${idx}][icon]" value="chat" class="row-icon-input">
                <button type="button" onclick="openIconPicker(this)" class="bg-surface-container-low hover:bg-surface-container px-2 py-1.5 rounded-xl border border-outline-variant/30 flex items-center gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-primary text-base row-icon-preview">chat</span>
                </button>
            </div>
        </td>
        <td class="p-2 text-center">
            <input type="color" name="chat_channels[${idx}][color]" value="#4c5e8b" class="w-8 h-8 p-0 border-none rounded-lg cursor-pointer">
        </td>
        <td class="p-2 text-center">
            <input type="number" name="chat_channels[${idx}][order]" value="10" class="w-12 bg-surface-container-low border-none rounded-xl p-2 text-xs text-center" dir="ltr">
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف کانال">
                <span class="material-symbols-outlined text-lg leading-none">delete</span>
            </button>
        </td>`;
}

function addNewCarrierRow() {
    const tbody = document.querySelector('#carriers-table tbody');
    const slug = 'carrier_' + Date.now();
    const row = tbody.insertRow();
    row.className = 'carrier-row';
    row.setAttribute('data-slug', slug);
    row.innerHTML = `
        <td class="p-2">
            <input type="text" value="${slug}" class="carrier-slug-input w-28 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono">
        </td>
        <td class="p-2">
            <input type="text" value="شرکت پستی جدید" class="carrier-name-input w-48 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-bold" required>
        </td>
        <td class="p-2">
            <input type="text" value="" placeholder="https://tracking.example.com/?id={code}" class="carrier-url-input w-full bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-left" dir="ltr">
        </td>
        <td class="p-2 text-center">
            <input type="text" value="local_shipping" class="carrier-icon-input w-24 bg-surface-container-low border-none rounded-xl p-2.5 text-xs font-mono text-center" dir="ltr">
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="removeRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center" title="حذف شرکت پستی">
                <span class="material-symbols-outlined text-lg leading-none">delete</span>
            </button>
        </td>`;
}

function saveCarriersList() {
    const rows = document.querySelectorAll('.carrier-row');
    const carriers = {};
    rows.forEach(r => {
        const slug = r.querySelector('.carrier-slug-input').value.trim().toLowerCase().replace(/[^a-z0-9_]/g, '');
        const name = r.querySelector('.carrier-name-input').value.trim();
        const url = r.querySelector('.carrier-url-input').value.trim();
        const icon = r.querySelector('.carrier-icon-input').value.trim() || 'local_shipping';
        if (slug && name) {
            carriers[slug] = { name, url, icon };
        }
    });

    const fd = new FormData();
    fd.append('action', 'serene_admin_save_carriers');
    fd.append('carriers', JSON.stringify(carriers));
    fd.append('nonce', config.nonce);

    fetch(config.ajax_url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showAdminNotification(d.data.message, 'success');
        } else {
            showAdminNotification(d.data.message || 'خطا در ذخیره شرکت‌های پستی.', 'error');
        }
    });
}

const PALETTE_PRESETS = [
    {
        name: 'اسلیت و ایندیگو مدرن (Modern Slate)',
        primary: '#3b82f6',
        secondary: '#1e293b',
        tertiary: '#6366f1',
        bg: '#f8fafc',
        card: '#ffffff',
        text: '#0f172a',
        admin_sidebar: '#1e293b',
        admin_accent: '#3b82f6',
        admin_topbar: '#0f172a'
    },
    {
        name: 'زمردی و ارگانیک لوکس (Emerald Luxe)',
        primary: '#059669',
        secondary: '#064e3b',
        tertiary: '#10b981',
        bg: '#f0fdf4',
        card: '#ffffff',
        text: '#064e3b',
        admin_sidebar: '#064e3b',
        admin_accent: '#10b981',
        admin_topbar: '#022c22'
    },
    {
        name: 'سایبری و بنفش مخملی (Cyber Velvet)',
        primary: '#7c3aed',
        secondary: '#2e1065',
        tertiary: '#a855f7',
        bg: '#faf5ff',
        card: '#ffffff',
        text: '#1e1b4b',
        admin_sidebar: '#1e1b4b',
        admin_accent: '#7c3aed',
        admin_topbar: '#0f0e17'
    },
    {
        name: 'غروب گرم و مرجانی (Warm Coral & Sunset)',
        primary: '#ea580c',
        secondary: '#7c2d12',
        tertiary: '#f97316',
        bg: '#fff7ed',
        card: '#ffffff',
        text: '#431407',
        admin_sidebar: '#431407',
        admin_accent: '#ea580c',
        admin_topbar: '#270a04'
    },
    {
        name: 'اقیانوس نوردیک و فیروزه‌ای (Nordic Ocean)',
        primary: '#0891b2',
        secondary: '#164e63',
        tertiary: '#06b6d4',
        bg: '#f0fdfa',
        card: '#ffffff',
        text: '#083344',
        admin_sidebar: '#164e63',
        admin_accent: '#06b6d4',
        admin_topbar: '#083344'
    },
    {
        name: 'رزبری و زرشکی شیک (Berry Crimson)',
        primary: '#e11d48',
        secondary: '#881337',
        tertiary: '#f43f5e',
        bg: '#fff1f2',
        card: '#ffffff',
        text: '#4c0519',
        admin_sidebar: '#4c0519',
        admin_accent: '#e11d48',
        admin_topbar: '#2e020d'
    },
    {
        name: 'طلایی لوکس بیزینس (Luxury Gold & Amber)',
        primary: '#d97706',
        secondary: '#78350f',
        tertiary: '#f59e0b',
        bg: '#fffbeb',
        card: '#ffffff',
        text: '#451a03',
        admin_sidebar: '#1c1917',
        admin_accent: '#d97706',
        admin_topbar: '#0c0a09'
    },
    {
        name: 'دارک مینیمال تیتانیوم (Titanium Dark)',
        primary: '#475569',
        secondary: '#0f172a',
        tertiary: '#64748b',
        bg: '#f1f5f9',
        card: '#ffffff',
        text: '#0f172a',
        admin_sidebar: '#0f172a',
        admin_accent: '#38bdf8',
        admin_topbar: '#020617'
    }
];

let lastPaletteIdx = -1;
function suggestPalette(scope) {
    let nextIdx;
    do {
        nextIdx = Math.floor(Math.random() * PALETTE_PRESETS.length);
    } while (nextIdx === lastPaletteIdx && PALETTE_PRESETS.length > 1);
    lastPaletteIdx = nextIdx;
    const p = PALETTE_PRESETS[nextIdx];

    if (scope === 'login') {
        const pBtn = document.querySelector('input[name="settings[login_custom_primary]"]');
        const pBg = document.querySelector('input[name="settings[login_custom_bg_color]"]') || document.querySelector('input[name="settings[login_custom_bg]"]');
        const pCard = document.querySelector('input[name="settings[login_custom_card]"]');
        const pText = document.querySelector('input[name="settings[login_custom_text]"]');
        if (pBtn) pBtn.value = p.primary;
        if (pBg) pBg.value = p.bg;
        if (pCard) pCard.value = p.card;
        if (pText) pText.value = p.text;
    } else if (scope === 'admin') {
        const sBg = document.querySelector('input[name="settings[admin_custom_sidebar_bg]"]');
        const sAcc = document.querySelector('input[name="settings[admin_custom_accent]"]');
        const sTop = document.querySelector('input[name="settings[admin_custom_topbar_bg]"]');
        if (sBg) sBg.value = p.admin_sidebar;
        if (sAcc) sAcc.value = p.admin_accent;
        if (sTop) sTop.value = p.admin_topbar;
    } else if (scope === 'global') {
        const gPri = document.querySelector('input[name="settings[color_primary]"]');
        const gSec = document.querySelector('input[name="settings[color_secondary]"]');
        const gTer = document.querySelector('input[name="settings[color_tertiary]"]');
        if (gPri) gPri.value = p.primary;
        if (gSec) gSec.value = p.secondary;
        if (gTer) gTer.value = p.tertiary;
    }

    showAdminNotification(`پالت پیشنهادی «${p.name}» اعمال شد.`, 'success');
}

function deleteWheelCoupon(id) {
    if (!confirm('آیا از حذف این کد تخفیف و رکورد برنده اطمینان دارید؟')) return;

    const fd = new FormData();
    fd.append('action', 'serene_admin_delete_wheel_coupon');
    fd.append('id', id);
    fd.append('nonce', config.nonce);

    fetch(config.ajax_url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('wheel-row-' + id);
            if (row) row.remove();
            showAdminNotification(res.data?.message || 'کد تخفیف با موفقیت حذف شد.', 'success');
        } else {
            showAdminNotification(res.data?.message || 'خطا در حذف کد تخفیف.', 'error');
        }
    });
}

function clearExpiredWheelCoupons() {
    if (!confirm('آیا از پاکسازی تمام کدهای تخفیف منقضی‌شده اطمینان دارید؟')) return;

    const fd = new FormData();
    fd.append('action', 'serene_admin_clear_expired_wheel_coupons');
    fd.append('nonce', config.nonce);

    fetch(config.ajax_url, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showAdminNotification(res.data?.message || 'کدهای منقضی با موفقیت پاکسازی شدند.', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showAdminNotification(res.data?.message || 'خطا در پاکسازی.', 'error');
        }
    });
}

function removeRow(btn) {
    const row = btn.closest('tr');
    if (row) row.remove();
    calculateWheelProbabilities();
}

window.addEventListener('DOMContentLoaded', () => {
    toggleSmsFields();
    calculateWheelProbabilities();
    restoreTabsFromHash();
});
</script>
