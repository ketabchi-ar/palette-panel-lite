<?php
if (!defined('ABSPATH')) exit;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$current_tab = Serene_Panel_Dashboard::get_current_tab();
$menu_items = Serene_Panel_Menu_Builder::get_menu_items();
$unread_notifs = Serene_Panel_Notifications::get_unread_count($user_id);
$wallet_balance = Serene_Panel_Wallet::get_balance($user_id);
$tier = Serene_Panel_Loyalty_Tiers::get_user_tier($user_id);
$is_standalone = !did_action('wp_head') && !did_action('get_header');
if ($is_standalone): ?>
<!DOCTYPE html>
<html class="light" dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>پنل کاربری پیشرفته - <?php echo esc_html(get_bloginfo('name')); ?></title>
    <?php wp_head(); ?>
</head>
<body class="bg-background text-on-surface min-h-screen text-right font-body serene-body">
<?php endif; ?>

<div class="serene-app bg-background text-on-surface min-h-screen text-right font-body relative" dir="rtl">

<!-- Mobile Sidebar Backdrop Overlay -->
<div id="sidebar-backdrop" onclick="toggleMobileSidebar()" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden transition-opacity"></div>

<!-- Top Navigation Bar -->
<header class="fixed top-0 right-0 left-0 h-16 bg-surface-container-lowest/90 backdrop-blur-md shadow-sm flex justify-between items-center px-4 md:px-6 z-40 transition-colors border-b border-outline-variant/30">
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Mobile Menu Hamburger Button -->
        <button type="button" onclick="toggleMobileSidebar()" class="md:hidden material-symbols-outlined text-on-surface-variant hover:bg-surface-container-low p-2 rounded-xl transition-colors cursor-pointer" aria-label="منوی اصلی">
            menu
        </button>

        <!-- Notification Dropdown Toggle -->
        <div class="relative">
            <button onclick="toggleNotifs()" class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-low p-2 rounded-full transition-colors relative cursor-pointer">
                notifications
                <?php if ($unread_notifs > 0): ?>
                    <span class="absolute top-1.5 left-1.5 w-2.5 h-2.5 bg-error rounded-full ring-2 ring-surface"></span>
                <?php endif; ?>
            </button>
            <div id="notif-dropdown" class="hidden absolute left-0 mt-2 w-72 md:w-80 bg-surface-container-lowest shadow-2xl rounded-2xl p-4 border border-outline-variant/40 z-50">
                <div class="flex justify-between items-center mb-3 pb-2 border-b border-outline-variant/20">
                    <span class="font-bold text-xs">اعلان‌ها و پیام‌ها</span>
                    <span class="text-[11px] text-primary font-bold"><?php echo $unread_notifs; ?> خوانده نشده</span>
                </div>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    <?php
                    $notifs = Serene_Panel_Notifications::get_user_notifications($user_id, 5);
                    if (empty($notifs)):
                    ?>
                        <p class="text-xs text-on-surface-variant text-center py-4">اعلان جدیدی وجود ندارد.</p>
                    <?php else: foreach ($notifs as $n): ?>
                        <div class="p-2.5 rounded-xl bg-surface-container-low text-xs space-y-1">
                            <div class="font-bold text-on-surface"><?php echo esc_html($n->title); ?></div>
                            <div class="text-on-surface-variant"><?php echo esc_html($n->message); ?></div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-low p-2 rounded-full transition-colors">shopping_cart</a>
        
        <!-- User Avatar Mini -->
        <a href="?tab=profile" class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-primary-container overflow-hidden ring-2 ring-primary/20 flex items-center justify-center">
            <img class="w-full h-full object-cover" src="<?php echo esc_url(get_avatar_url($user_id)); ?>" alt="Avatar"/>
        </a>
    </div>

    <div class="flex items-center gap-2">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="text-sm md:text-base font-black text-primary hover:opacity-90 transition-opacity">
            <?php echo esc_html(get_bloginfo('name')); ?>
        </a>
    </div>
</header>

<!-- Sidebar (Responsive Drawer on Mobile, Fixed on Desktop) -->
<aside id="serene-sidebar" class="fixed top-0 right-0 h-screen w-72 md:w-64 bg-surface-container-lowest border-l border-outline-variant/30 flex flex-col z-50 pt-4 md:pt-20 shadow-2xl md:shadow-sm transform translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    
    <!-- Mobile Sidebar Close Header -->
    <div class="flex md:hidden justify-between items-center px-5 pb-3 mb-2 border-b border-outline-variant/20">
        <span class="font-bold text-xs text-primary">منوی ناوبری پنل</span>
        <button type="button" onclick="toggleMobileSidebar()" class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-low p-1.5 rounded-xl cursor-pointer">
            close
        </button>
    </div>

    <!-- User Profile Header in Sidebar -->
    <div class="px-5 md:px-6 mb-4 md:mb-6 flex items-center gap-3">
        <div class="w-11 h-11 md:w-12 md:h-12 rounded-2xl overflow-hidden ring-2 ring-primary/20 bg-primary-container flex items-center justify-center text-on-primary-container font-bold shrink-0">
            <img class="w-full h-full object-cover" src="<?php echo esc_url(get_avatar_url($user_id)); ?>" alt="Avatar"/>
        </div>
        <div class="overflow-hidden">
            <div class="font-bold text-xs md:text-sm text-on-surface truncate"><?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?></div>
            <div class="text-[11px] font-medium flex items-center gap-1 mt-0.5" style="color: <?php echo esc_attr($tier['color'] ?? '#4c5e8b'); ?>;">
                <span class="material-symbols-outlined text-sm"><?php echo esc_html($tier['icon'] ?? 'stars'); ?></span>
                <span>سطح <?php echo esc_html($tier['name'] ?? 'عادی'); ?></span>
            </div>
        </div>
    </div>

    <!-- Navigation Menu Items -->
    <nav class="flex-1 px-3 md:px-4 space-y-1 overflow-y-auto">
        <?php 
        $user_menu = Serene_Panel_Menu_Builder::get_user_visible_menu_items($user_id);
        foreach ($user_menu as $key => $item): 
            $is_active = ($current_tab === $key);
            $link = '?tab=' . esc_attr($item['target'] ?: $key);
            $target_attr = '';
            if (($item['type'] ?? '') === 'custom_link') {
                $link = esc_url($item['target']);
                $target_attr = 'target="_blank"';
            }
        ?>
        <a class="flex items-center justify-between px-3.5 py-2.5 md:py-3 rounded-xl text-xs md:text-sm font-medium transition-all <?php echo $is_active ? 'bg-primary-container text-on-primary-container font-bold border-r-4 border-primary shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high'; ?>" href="<?php echo $link; ?>" <?php echo $target_attr; ?>>
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-lg md:text-xl" <?php echo $is_active ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>><?php echo esc_html($item['icon']); ?></span>
                <span><?php echo esc_html($item['title']); ?></span>
            </div>
            <?php if (!empty($item['badge'])): ?>
                <span class="bg-tertiary-container text-on-tertiary-container text-[10px] font-bold px-2 py-0.5 rounded-full"><?php echo esc_html($item['badge']); ?></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-outline-variant/30 space-y-1.5">
        <a class="flex items-center gap-2.5 text-on-surface-variant hover:bg-surface-container-high px-3 py-2 rounded-xl text-xs transition-all" href="<?php echo esc_url(home_url('/')); ?>">
            <span class="material-symbols-outlined text-base">storefront</span>
            <span>بازگشت به فروشگاه</span>
        </a>
        <a class="flex items-center gap-2.5 text-error hover:bg-error-container/20 px-3 py-2 rounded-xl text-xs transition-all" href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">
            <span class="material-symbols-outlined text-base">logout</span>
            <span>خروج از حساب</span>
        </a>
    </div>
</aside>

<!-- Main Canvas Content -->
<main class="md:mr-64 pt-20 px-3 md:px-8 pb-24 md:pb-12 min-h-screen">
    <div class="max-w-6xl mx-auto space-y-8">
        <?php
        switch ($current_tab) {
            case 'orders':
                include SERENE_PANEL_TEMPLATES_PATH . 'orders/orders-list.php';
                break;
            case 'wallet':
                include SERENE_PANEL_TEMPLATES_PATH . 'wallet/wallet-view.php';
                break;
            case 'tickets':
                include SERENE_PANEL_TEMPLATES_PATH . 'tickets/tickets-list.php';
                break;
            case 'lucky_wheel':
                include SERENE_PANEL_TEMPLATES_PATH . 'gamification/lucky-wheel.php';
                break;
            case 'rewards':
                include SERENE_PANEL_TEMPLATES_PATH . 'dashboard/tab-rewards.php';
                break;
            case 'profile':
                include SERENE_PANEL_TEMPLATES_PATH . 'dashboard/tab-profile.php';
                break;
            case 'addresses':
                include SERENE_PANEL_TEMPLATES_PATH . 'dashboard/tab-addresses.php';
                break;
            case 'downloads':
                include SERENE_PANEL_TEMPLATES_PATH . 'dashboard/tab-downloads.php';
                break;
            case 'affiliate':
                include SERENE_PANEL_TEMPLATES_PATH . 'dashboard/tab-affiliate.php';
                break;
            case 'dashboard':
                include SERENE_PANEL_TEMPLATES_PATH . 'dashboard/tab-home.php';
                break;
            default:
                $custom_items = Serene_Panel_Menu_Builder::get_menu_items();
                if (isset($custom_items[$current_tab]) && ($custom_items[$current_tab]['type'] ?? '') === 'shortcode') {
                    echo '<div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 shadow-sm">';
                    echo do_shortcode($custom_items[$current_tab]['target']);
                    echo '</div>';
                } else {
                    include SERENE_PANEL_TEMPLATES_PATH . 'dashboard/tab-home.php';
                }
                break;
        }
        ?>
    </div>
</main>

<!-- Mobile Bottom Navigation Bar (Thumb Friendly) -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-surface-container-lowest/95 backdrop-blur-lg border-t border-outline-variant/30 z-30 px-2 py-1.5 flex justify-around items-center shadow-lg">
    <a href="?tab=dashboard" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl text-[10px] font-bold <?php echo $current_tab === 'dashboard' ? 'text-primary' : 'text-on-surface-variant'; ?>">
        <span class="material-symbols-outlined text-xl" <?php echo $current_tab === 'dashboard' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>dashboard</span>
        <span>پیشخوان</span>
    </a>
    <a href="?tab=orders" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl text-[10px] font-bold <?php echo $current_tab === 'orders' ? 'text-primary' : 'text-on-surface-variant'; ?>">
        <span class="material-symbols-outlined text-xl" <?php echo $current_tab === 'orders' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>shopping_bag</span>
        <span>سفارش‌ها</span>
    </a>
    <a href="?tab=lucky_wheel" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl text-[10px] font-bold <?php echo $current_tab === 'lucky_wheel' ? 'text-primary' : 'text-on-surface-variant'; ?>">
        <span class="material-symbols-outlined text-xl" <?php echo $current_tab === 'lucky_wheel' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>casino</span>
        <span>گردونه</span>
    </a>
    <a href="?tab=wallet" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl text-[10px] font-bold <?php echo $current_tab === 'wallet' ? 'text-primary' : 'text-on-surface-variant'; ?>">
        <span class="material-symbols-outlined text-xl" <?php echo $current_tab === 'wallet' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>account_balance_wallet</span>
        <span>کیف پول</span>
    </a>
    <a href="?tab=profile" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl text-[10px] font-bold <?php echo $current_tab === 'profile' ? 'text-primary' : 'text-on-surface-variant'; ?>">
        <span class="material-symbols-outlined text-xl" <?php echo $current_tab === 'profile' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>person</span>
        <span>پروفایل</span>
    </a>
</nav>

</div>

<script>
function toggleNotifs() {
    document.getElementById('notif-dropdown').classList.toggle('hidden');
}

function toggleMobileSidebar() {
    const sidebar = document.getElementById('serene-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!sidebar || !backdrop) return;

    if (sidebar.classList.contains('translate-x-full')) {
        sidebar.classList.remove('translate-x-full');
        sidebar.classList.add('translate-x-0');
        backdrop.classList.remove('hidden');
    } else {
        sidebar.classList.add('translate-x-full');
        sidebar.classList.remove('translate-x-0');
        backdrop.classList.add('hidden');
    }
}
</script>
<?php if ($is_standalone): wp_footer(); ?>
</body>
</html>
<?php endif; ?>
