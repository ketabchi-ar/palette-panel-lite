<?php
if (!defined('ABSPATH')) {
    exit;
}

class Palette_Panel_Smart_Reviews {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('woocommerce_single_product_summary', [$this, 'render_product_reward_badge'], 35);
        add_action('comment_form_after_fields', [$this, 'add_review_photo_field']);
        add_action('comment_post', [$this, 'save_review_meta_and_process_reward'], 10, 3);
        add_action('transition_comment_status', [$this, 'handle_comment_approval'], 10, 3);
    }

    public function render_product_reward_badge() {
        $opt = get_option('serene_panel_options', []);
        if (empty($opt['enable_smart_reviews'])) return;

        $reward = floatval($opt['review_reward_amount'] ?? 10000);
        if ($reward <= 0) return;

        ?>
        <div class="palette-review-reward-badge" style="margin: 12px 0; padding: 10px 14px; background: #ecfdf5; border: 1px dashed #10b981; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; font-size: 12px; color: #065f46; font-family: inherit;">
            <span style="font-size: 16px;">🎁</span>
            <span>با ثبت نقد و بررسی برای این محصول، <strong><?php echo number_format($reward); ?> تومان</strong> هدیه در کیف پول دریافت کنید!</span>
        </div>
        <?php
    }

    public function add_review_photo_field() {
        $opt = get_option('serene_panel_options', []);
        if (empty($opt['enable_smart_reviews'])) return;

        ?>
        <p class="comment-form-photo" style="margin: 10px 0; font-family: inherit;">
            <label for="review_photo" style="display:block; font-size:12px; font-weight:bold; margin-bottom:4px;">📸 بارگذاری تصویر واقعی محصول (اختیاری جهت دریافت پاداش دوبرابر):</label>
            <input type="file" name="review_photo" id="review_photo" accept="image/*" style="font-size:12px;">
        </p>
        <?php
    }

    public function save_review_meta_and_process_reward($comment_id, $approved, $commentdata) {
        if ($commentdata['comment_type'] !== 'review') return;

        if (!empty($_FILES['review_photo']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $upload = wp_handle_upload($_FILES['review_photo'], ['test_form' => false]);
            if (!isset($upload['error']) && isset($upload['url'])) {
                update_comment_meta($comment_id, '_review_photo_url', $upload['url']);
            }
        }

        if ($approved === 1 || $approved === '1') {
            self::award_reward_to_user($comment_id, $commentdata['user_id'], $commentdata['comment_post_ID']);
        }
    }

    public function handle_comment_approval($new_status, $old_status, $comment) {
        if ($new_status === 'approved' && $old_status !== 'approved' && $comment->comment_type === 'review') {
            self::award_reward_to_user($comment->comment_ID, $comment->user_id, $comment->comment_post_ID);
        }
    }

    public static function award_reward_to_user($comment_id, $user_id, $product_id) {
        if (!$user_id) return;
        $already_awarded = get_comment_meta($comment_id, '_reward_awarded', true);
        if ($already_awarded) return;

        $opt = get_option('serene_panel_options', []);
        if (empty($opt['enable_smart_reviews'])) return;

        $reward = floatval($opt['review_reward_amount'] ?? 10000);
        if ($reward <= 0) return;

        $min_rating = intval($opt['review_min_rating'] ?? 3);
        $rating = intval(get_comment_meta($comment_id, 'rating', true));
        if ($rating > 0 && $rating < $min_rating) return;

        // Check verified purchase if required
        if (!empty($opt['review_require_purchase']) && function_exists('wc_customer_bought_product')) {
            $user = get_user_by('ID', $user_id);
            if ($user && !wc_customer_bought_product($user->user_email, $user_id, $product_id)) {
                return;
            }
        }

        update_comment_meta($comment_id, '_reward_awarded', 1);

        if (class_exists('Serene_Panel_Wallet')) {
            Serene_Panel_Wallet::update_balance(
                $user_id,
                $reward,
                'credit',
                sprintf('پاداش ثبت نقد و بررسی محصول #%d', $product_id)
            );
        }

        if (class_exists('Serene_Panel_System_Logger')) {
            Serene_Panel_System_Logger::success('REVIEWS', sprintf('مبلغ %s تومان پاداش نظر به کاربر #%d برای محصول #%d اهدا شد.', number_format($reward), $user_id, $product_id));
        }
    }
}

// Backward Compatibility Class Alias
class Serene_Panel_Smart_Reviews extends Palette_Panel_Smart_Reviews {}
