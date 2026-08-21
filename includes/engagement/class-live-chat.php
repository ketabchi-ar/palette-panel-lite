<?php
if (!defined('ABSPATH')) {
    exit;
}

class Palette_Panel_Live_Chat {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_footer', [$this, 'render_floating_chat_widget']);
    }

    public static function get_default_channels() {
        return [
            'tickets' => [
                'id'       => 'tickets',
                'title'    => 'ارسال تیکت پشتیبانی',
                'subtitle' => 'پیگیری تخصصی و ۲۴ ساعته',
                'icon'     => 'confirmation_number',
                'type'     => 'ticket',
                'url'      => '',
                'color'    => '#4c5e8b',
                'enabled'  => 1,
            ],
            'whatsapp' => [
                'id'       => 'whatsapp',
                'title'    => 'گفتگو در واتساپ',
                'subtitle' => 'پاسخگویی سریع آنلاین',
                'icon'     => 'chat',
                'type'     => 'whatsapp',
                'url'      => '989120000000',
                'color'    => '#25D366',
                'enabled'  => 1,
            ],
            'telegram' => [
                'id'       => 'telegram',
                'title'    => 'کانال یا ربات تلگرام',
                'subtitle' => 'پشتیبانی و اطلاع‌رسانی',
                'icon'     => 'send',
                'type'     => 'telegram',
                'url'      => '@PaletteAgency',
                'color'    => '#0088cc',
                'enabled'  => 1,
            ],
            'eitaa' => [
                'id'       => 'eitaa',
                'title'    => 'پشتیبانی در ایتا (Eitaa)',
                'subtitle' => 'ارتباط در پیام‌رسان داخلی',
                'icon'     => 'forum',
                'type'     => 'custom',
                'url'      => 'https://eitaa.com/palette',
                'color'    => '#ea580c',
                'enabled'  => 0,
            ],
            'phone' => [
                'id'       => 'phone',
                'title'    => 'تماس تلفنی با پشتیبانی',
                'subtitle' => 'شنبه تا چهارشنبه ۹ الی ۱۷',
                'icon'     => 'call',
                'type'     => 'phone',
                'url'      => '02112345678',
                'color'    => '#0284c7',
                'enabled'  => 1,
            ],
        ];
    }

    public static function get_channels() {
        $channels = get_option('serene_panel_chat_channels', null);
        if ($channels === null || !is_array($channels)) {
            $opt = get_option('serene_panel_options', []);
            if (!empty($opt['chat_channels']) && is_array($opt['chat_channels'])) {
                $channels = $opt['chat_channels'];
            } else {
                $channels = self::get_default_channels();
            }
        }
        return $channels;
    }

    public function render_floating_chat_widget() {
        $opt = get_option('serene_panel_options', []);
        if (empty($opt['enable_live_chat'])) return;

        $pos = $opt['chat_position'] ?? 'left';
        $title = $opt['chat_title'] ?? 'پشتیبانی آنلاین و راهنما';
        $subtitle = $opt['chat_subtitle'] ?? 'ما همیشه آماده پاسخگویی به شما هستیم';
        $primary_color = $opt['color_primary'] ?? '#4c5e8b';
        $channels = self::get_channels();

        $pos_style = ($pos === 'right') ? 'right: 25px;' : 'left: 25px;';
        ?>
        <!-- Palette Live Chat Dynamic Multi-Channel Widget -->
        <div id="palette-chat-widget-root" style="position: fixed; bottom: 25px; <?php echo $pos_style; ?> z-index: 9999; font-family: inherit; direction: rtl;">
            <!-- Chat Box Popup -->
            <div id="palette-chat-box" style="display: none; width: 330px; background: #ffffff; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.18); border: 1px solid rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 16px; animation: paletteSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                <!-- Header -->
                <div style="background: <?php echo esc_attr($primary_color); ?>; color: #fff; padding: 20px; text-align: right; position: relative;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <h4 style="margin: 0; font-size: 15px; font-weight: 900; color: #fff; display: flex; items-center; gap: 6px;">
                            <span class="material-symbols-outlined" style="font-size: 20px;">support_agent</span>
                            <span><?php echo esc_html($title); ?></span>
                        </h4>
                        <button type="button" onclick="togglePaletteChat()" style="background: rgba(255,255,255,0.2); border: none; color: #fff; width: 28px; height: 28px; border-radius: 50%; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">✕</button>
                    </div>
                    <p style="margin: 0; font-size: 11px; opacity: 0.9; line-height: 1.5;"><?php echo esc_html($subtitle); ?></p>
                </div>

                <!-- Dynamic Channels List -->
                <div style="padding: 14px; display: flex; flex-direction: column; gap: 10px; background: #f8fafc; max-height: 380px; overflow-y: auto;">
                    <?php 
                    $has_active_channel = false;
                    foreach ($channels as $ch):
                        if (empty($ch['enabled'])) continue;
                        $has_active_channel = true;
                        $ch_url = $ch['url'] ?? '';
                        $ch_type = $ch['type'] ?? 'custom';
                        $target = '_blank';

                        if ($ch_type === 'ticket') {
                            $link = esc_url(add_query_arg(['tab' => 'tickets'], wc_get_page_permalink('myaccount')));
                            $target = '_self';
                        } elseif ($ch_type === 'whatsapp') {
                            $clean_num = preg_replace('/[^0-9]/', '', $ch_url);
                            $link = 'https://wa.me/' . $clean_num;
                        } elseif ($ch_type === 'telegram') {
                            $clean_tg = str_replace('@', '', $ch_url);
                            $link = 'https://t.me/' . $clean_tg;
                        } elseif ($ch_type === 'phone') {
                            $link = 'tel:' . esc_attr($ch_url);
                            $target = '_self';
                        } elseif ($ch_type === 'sms') {
                            $link = 'sms:' . esc_attr($ch_url);
                            $target = '_self';
                        } else {
                            $link = esc_url($ch_url);
                        }
                    ?>
                    <a href="<?php echo $link; ?>" target="<?php echo esc_attr($target); ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; text-decoration: none; color: #1e293b; font-size: 12px; font-weight: bold; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 2px 4px rgba(0,0,0,0.02);" onmouseover="this.style.borderColor='<?php echo esc_attr($ch['color'] ?? $primary_color); ?>'; this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='none';">
                        <div style="width: 38px; height: 38px; border-radius: 12px; background: <?php echo esc_attr($ch['color'] ?? $primary_color); ?>15; color: <?php echo esc_attr($ch['color'] ?? $primary_color); ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="material-symbols-outlined" style="font-size: 22px;"><?php echo esc_html($ch['icon'] ?? 'chat'); ?></span>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="color: #0f172a; font-size: 12px; font-weight: 800; truncate"><?php echo esc_html($ch['title']); ?></div>
                            <?php if (!empty($ch['subtitle'])): ?>
                            <div style="font-size: 10px; color: #64748b; font-weight: 500; margin-top: 2px;"><?php echo esc_html($ch['subtitle']); ?></div>
                            <?php endif; ?>
                        </div>
                        <span class="material-symbols-outlined" style="font-size: 16px; color: #94a3b8; transform: rotate(180deg);">arrow_forward</span>
                    </a>
                    <?php endforeach; ?>

                    <?php if (!$has_active_channel): ?>
                        <div style="text-align:center; padding: 20px; font-size: 11px; color: #64748b;">کانالی جهت نمایش فعال نشده است.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Floating Trigger Button -->
            <button type="button" onclick="togglePaletteChat()" id="palette-chat-btn" style="width: 58px; height: 58px; border-radius: 50%; background: <?php echo esc_attr($primary_color); ?>; color: #fff; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.22); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                <span id="palette-chat-icon" class="material-symbols-outlined" style="font-size: 28px;">chat</span>
            </button>
        </div>

        <script>
        function togglePaletteChat() {
            const box = document.getElementById('palette-chat-box');
            const icon = document.getElementById('palette-chat-icon');
            if (box.style.display === 'none' || !box.style.display) {
                box.style.display = 'block';
                if (icon) icon.innerText = 'close';
            } else {
                box.style.display = 'none';
                if (icon) icon.innerText = 'chat';
            }
        }
        </script>
        <style>
        @keyframes paletteSlideUp {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        </style>
        <?php
    }
}

// Backward Compatibility Class Alias
class Serene_Panel_Live_Chat extends Palette_Panel_Live_Chat {}
