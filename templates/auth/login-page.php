<?php
if (!defined('ABSPATH')) exit;
$options = get_option('serene_panel_options', []);
$enable_google   = !empty($options['enable_google_auth']) && !empty($options['google_client_id']);
$enable_bale     = !empty($options['enable_bale_auth']);
$enable_telegram = !empty($options['enable_telegram_auth']);
$enable_voice    = !empty($options['enable_voice_auth']);
$enable_biometric= !empty($options['enable_biometric']);
$google_client_id= $options['google_client_id'] ?? '';

$login_theme     = $options['login_theme_style'] ?? 'split_modern';
$login_logo      = $options['login_custom_logo'] ?? '';
$login_bg_img    = $options['login_custom_bg'] ?? '';
$login_title     = $options['login_welcome_title'] ?? ('ورود به ' . get_bloginfo('name'));
$login_subtitle  = $options['login_welcome_subtitle'] ?? 'جهت دسترسی به سفارشات، کیف پول و تخفیف‌ها وارد شوید';
$login_terms_url = $options['login_terms_url'] ?? '#';

$login_pri_col  = $options['login_custom_primary'] ?? '';
$login_bg_col   = $options['login_custom_bg_color'] ?? ($options['login_custom_bg'] ?? '');
$login_card_col = $options['login_custom_card'] ?? '';
$login_text_col = $options['login_custom_text'] ?? '';

$is_standalone = !did_action('wp_head') && !did_action('get_header');
if ($is_standalone): ?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo esc_html($login_title); ?> - <?php echo esc_html(get_bloginfo('name')); ?></title>
    <?php wp_head(); ?>
    <?php if ($enable_google): ?>
        <script src="https://accounts.google.com/gsi/client" async defer></script>
    <?php endif; ?>
</head>
<body class="bg-background text-on-surface min-h-screen flex items-center justify-center p-0 relative overflow-x-hidden font-body serene-body <?php echo esc_attr($login_theme === 'dark_modern' ? 'bg-[#0b0f19] text-white' : ''); ?>">
<?php endif; ?>

<?php if ($login_pri_col || $login_bg_col || $login_card_col || $login_text_col): ?>
<style id="palette-login-custom-colors">
:root {
    <?php if ($login_pri_col): ?>
    --serene-primary: <?php echo esc_attr($login_pri_col); ?> !important;
    --serene-primary-dim: <?php echo esc_attr($login_pri_col); ?> !important;
    <?php endif; ?>
}
<?php if ($login_bg_col && strpos($login_bg_col, '#') === 0): ?>
.serene-app, body.serene-body { background-color: <?php echo esc_attr($login_bg_col); ?> !important; }
<?php endif; ?>
<?php if ($login_card_col): ?>
.bg-surface-container-lowest, .bg-surface-container-low, .login-card-box { background-color: <?php echo esc_attr($login_card_col); ?> !important; }
<?php endif; ?>
<?php if ($login_text_col): ?>
.serene-app, .text-on-surface { color: <?php echo esc_attr($login_text_col); ?> !important; }
<?php endif; ?>
<?php if ($login_pri_col): ?>
.bg-primary { background-color: <?php echo esc_attr($login_pri_col); ?> !important; }
.text-primary { color: <?php echo esc_attr($login_pri_col); ?> !important; }
.border-primary { border-color: <?php echo esc_attr($login_pri_col); ?> !important; }
<?php endif; ?>
</style>
<?php endif; ?>

<div class="serene-app w-full min-h-screen flex items-center justify-center relative font-body <?php echo esc_attr($login_theme === 'dark_modern' ? 'bg-[#0b0f19]' : 'bg-slate-50'); ?>" dir="rtl" style="<?php echo ($login_bg_img && strpos($login_bg_img, 'http') === 0) ? "background-image: url('{$login_bg_img}'); background-size: cover; background-position: center;" : ''; ?>">

<!-- Ambient Glow Orbs -->
<div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] right-[-10%] w-[45%] h-[45%] bg-primary/15 rounded-full blur-[140px]"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[45%] h-[45%] bg-tertiary/15 rounded-full blur-[140px]"></div>
</div>

<?php if ($login_theme === 'split_modern'): ?>
    <!-- ========================================== -->
    <!-- DESIGN 1: SPLIT MODERN (Hero + Form) -->
    <!-- ========================================== -->
    <div class="relative z-10 w-full max-w-5xl mx-auto my-8 p-4">
        <div class="bg-surface-container-lowest rounded-[2.5rem] shadow-2xl border border-outline-variant/30 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[620px]">
            
            <!-- Right Hero Banner -->
            <div class="lg:col-span-5 bg-gradient-to-br from-primary via-primary-dim to-slate-900 p-8 lg:p-12 text-white flex flex-col justify-between relative overflow-hidden">
                <div class="absolute inset-0 bg-white/5 backdrop-blur-[2px]"></div>
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center gap-3">
                        <?php if ($login_logo): ?>
                            <img src="<?php echo esc_url($login_logo); ?>" class="h-12 max-w-[160px] object-contain">
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shadow-md">
                                <span class="material-symbols-outlined text-2xl">shield_lock</span>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="text-base font-black"><?php echo esc_html(get_bloginfo('name')); ?></div>
                            <div class="text-[11px] text-white/70">پلتفرم هوشمند کاربری</div>
                        </div>
                    </div>

                    <div class="pt-6 space-y-3">
                        <h2 class="text-2xl font-black leading-tight"><?php echo esc_html($login_title); ?></h2>
                        <p class="text-xs text-white/80 leading-relaxed"><?php echo esc_html($login_subtitle); ?></p>
                    </div>

                    <!-- Store Key Features -->
                    <div class="space-y-2.5 pt-4">
                        <div class="flex items-center gap-3 bg-white/10 p-2.5 rounded-xl text-xs backdrop-blur-sm">
                            <span class="material-symbols-outlined text-emerald-300 text-lg">verified</span>
                            <span>ورود بدون رمز عبور با کد یکبارمصرف سریع</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white/10 p-2.5 rounded-xl text-xs backdrop-blur-sm">
                            <span class="material-symbols-outlined text-amber-300 text-lg">local_shipping</span>
                            <span>رهگیری لحظه‌ای مرسولات و مشاهده فاکتور رسمی</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white/10 p-2.5 rounded-xl text-xs backdrop-blur-sm">
                            <span class="material-symbols-outlined text-sky-300 text-lg">account_balance_wallet</span>
                            <span>پاداش کش‌بک و شارژ خودکار کیف پول</span>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 pt-6 border-t border-white/20 text-[11px] text-white/70 flex justify-between items-center">
                    <span>پشتیبانی و امنیت هوشمند</span>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="underline hover:text-white">بازگشت به سایت</a>
                </div>
            </div>

            <!-- Left Auth Form Box -->
            <div class="lg:col-span-7 p-8 lg:p-12 flex flex-col justify-center bg-surface-container-lowest">
                <?php include __DIR__ . '/login-form-inner.php'; ?>
            </div>
        </div>
    </div>

<?php elseif ($login_theme === 'glass_minimal'): ?>
    <!-- ========================================== -->
    <!-- DESIGN 2: GLASSMORPHISM MINIMAL -->
    <!-- ========================================== -->
    <main class="relative z-10 w-full max-w-md my-8 p-4">
        <div class="flex flex-col items-center mb-6 text-center">
            <?php if ($login_logo): ?>
                <img src="<?php echo esc_url($login_logo); ?>" class="h-14 max-w-[200px] object-contain mb-3">
            <?php else: ?>
                <div class="w-14 h-14 bg-primary text-white rounded-2xl flex items-center justify-center mb-3 shadow-xl">
                    <span class="material-symbols-outlined text-3xl">fingerprint</span>
                </div>
            <?php endif; ?>
            <h1 class="text-xl font-black text-on-surface"><?php echo esc_html($login_title); ?></h1>
            <p class="text-xs text-on-surface-variant mt-1"><?php echo esc_html($login_subtitle); ?></p>
        </div>

        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl rounded-[2.5rem] p-8 border border-white/40 dark:border-slate-800 shadow-2xl space-y-6">
            <?php include __DIR__ . '/login-form-inner.php'; ?>
        </div>
    </main>

<?php elseif ($login_theme === 'dark_modern'): ?>
    <!-- ========================================== -->
    <!-- DESIGN 3: DARK SAAS MODERN -->
    <!-- ========================================== -->
    <main class="relative z-10 w-full max-w-md my-8 p-4">
        <div class="flex flex-col items-center mb-6 text-center">
            <?php if ($login_logo): ?>
                <img src="<?php echo esc_url($login_logo); ?>" class="h-14 max-w-[200px] object-contain mb-3">
            <?php else: ?>
                <div class="w-14 h-14 bg-gradient-to-tr from-primary to-indigo-500 text-white rounded-2xl flex items-center justify-center mb-3 shadow-xl shadow-primary/20">
                    <span class="material-symbols-outlined text-3xl">lock</span>
                </div>
            <?php endif; ?>
            <h1 class="text-xl font-black text-white"><?php echo esc_html($login_title); ?></h1>
            <p class="text-xs text-slate-400 mt-1"><?php echo esc_html($login_subtitle); ?></p>
        </div>

        <div class="bg-[#131b2e] text-slate-100 rounded-[2.5rem] p-8 border border-slate-800 shadow-2xl space-y-6">
            <?php include __DIR__ . '/login-form-inner.php'; ?>
        </div>
    </main>

<?php else: ?>
    <!-- ========================================== -->
    <!-- DESIGN 4: FLOATING CLEAN CARD -->
    <!-- ========================================== -->
    <main class="relative z-10 w-full max-w-md my-8 p-4">
        <div class="flex flex-col items-center mb-6 text-center">
            <?php if ($login_logo): ?>
                <img src="<?php echo esc_url($login_logo); ?>" class="h-14 max-w-[200px] object-contain mb-3">
            <?php else: ?>
                <div class="w-14 h-14 bg-primary-container text-on-primary-container rounded-2xl flex items-center justify-center mb-3 shadow-lg">
                    <span class="material-symbols-outlined text-3xl">shield_lock</span>
                </div>
            <?php endif; ?>
            <h1 class="text-xl font-black text-primary"><?php echo esc_html($login_title); ?></h1>
            <p class="text-xs text-on-surface-variant mt-1"><?php echo esc_html($login_subtitle); ?></p>
        </div>

        <div class="bg-surface-container-lowest rounded-[2.2rem] p-8 border border-outline-variant/30 shadow-xl space-y-6">
            <?php include __DIR__ . '/login-form-inner.php'; ?>
        </div>
    </main>
<?php endif; ?>

</div>

<?php if ($is_standalone): ?>
    <?php wp_footer(); ?>
</body>
</html>
<?php endif; ?>
