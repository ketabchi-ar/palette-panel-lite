<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Tickets {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function get_departments() {
        $saved = get_option('serene_panel_ticket_departments', null);
        if ($saved && is_array($saved) && !empty($saved)) {
            return $saved;
        }

        return [
            'support'   => 'پشتیبانی فنی و کاربری',
            'financial' => 'امور مالی و حسابداری',
            'sales'     => 'واحد فروش و سفارشات',
            'warranty'  => 'خدمات پس از فروش و گارانتی',
        ];
    }

    public static function save_departments($departments) {
        if (!is_array($departments)) return false;
        $clean = [];
        foreach ($departments as $key => $title) {
            $slug = sanitize_key($key);
            $name = sanitize_text_field($title);
            if (!empty($slug) && !empty($name)) {
                $clean[$slug] = $name;
            }
        }
        return update_option('serene_panel_ticket_departments', $clean);
    }

    public static function get_status_info($status) {
        switch ($status) {
            case 'open':
                return ['label' => 'جدید / باز', 'class' => 'bg-blue-100 text-blue-800 border-blue-300'];
            case 'pending':
                return ['label' => 'در انتظار پاسخ', 'class' => 'bg-amber-100 text-amber-800 border-amber-300'];
            case 'in_progress':
                return ['label' => 'در حال بررسی', 'class' => 'bg-purple-100 text-purple-800 border-purple-300'];
            case 'replied':
                return ['label' => 'پاسخ داده شده', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-300'];
            case 'closed':
                return ['label' => 'بسته شده', 'class' => 'bg-slate-100 text-slate-700 border-slate-300'];
            default:
                return ['label' => $status, 'class' => 'bg-slate-100 text-slate-700 border-slate-300'];
        }
    }

    public static function get_priority_info($priority) {
        switch ($priority) {
            case 'high':
            case 'urgent':
                return ['label' => 'فوری / بالا', 'class' => 'bg-rose-100 text-rose-800 border-rose-300'];
            case 'low':
                return ['label' => 'کم', 'class' => 'bg-slate-100 text-slate-700 border-slate-300'];
            case 'medium':
            default:
                return ['label' => 'متوسط', 'class' => 'bg-sky-100 text-sky-800 border-sky-300'];
        }
    }

    public static function get_user_tickets($user_id, $limit = 20) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_tickets';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY updated_at DESC LIMIT %d",
            $user_id, $limit
        ));
    }

    public static function get_all_tickets($limit = 100, $status = '', $department = '', $search = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_tickets';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            Serene_Panel_Activator::create_tables();
        }

        $where = ["1=1"];
        $params = [];

        if (!empty($status) && $status !== 'all') {
            $where[] = "status = %s";
            $params[] = $status;
        }

        if (!empty($department) && $department !== 'all') {
            $where[] = "department = %s";
            $params[] = $department;
        }

        if (!empty($search)) {
            $where[] = "(subject LIKE %s OR id = %d)";
            $params[] = '%' . $wpdb->esc_like($search) . '%';
            $params[] = intval($search);
        }

        $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where) . " ORDER BY updated_at DESC LIMIT %d";
        $params[] = $limit;

        $results = $wpdb->get_results($wpdb->prepare($sql, $params));
        if (empty($results)) return [];

        $departments = self::get_departments();

        foreach ($results as $ticket) {
            $user = get_user_by('ID', $ticket->user_id);
            $ticket->user_name = $user ? ($user->display_name ?: $user->user_login) : 'کاربر مهمان #' . $ticket->user_id;
            $ticket->user_email = $user ? $user->user_email : '';
            $ticket->user_phone = $user ? get_user_meta($user->ID, 'billing_phone', true) : '';
            $ticket->user_avatar = $user ? get_avatar_url($user->ID, ['size' => 64]) : '';
            $ticket->department_name = $departments[$ticket->department] ?? $ticket->department;
            $ticket->status_info = self::get_status_info($ticket->status);
            $ticket->priority_info = self::get_priority_info($ticket->priority);
        }

        return $results;
    }

    public static function get_ticket_stats() {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_tickets';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            Serene_Panel_Activator::create_tables();
        }

        $total   = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        $pending = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE status IN ('open', 'pending')");
        $replied = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE status = 'replied'");
        $closed  = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE status = 'closed'");

        return [
            'total'       => $total,
            'pending'     => $pending,
            'replied'     => $replied,
            'closed'      => $closed,
            'departments' => count(self::get_departments()),
        ];
    }

    public static function get_pending_tickets_count() {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_tickets';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) return 0;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE status IN ('open', 'pending')");
    }

    public static function get_ticket($ticket_id, $user_id = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_tickets';

        if ($user_id > 0) {
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE id = %d AND user_id = %d LIMIT 1",
                $ticket_id, $user_id
            ));
        }

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d LIMIT 1", $ticket_id));
    }

    public static function get_ticket_messages($ticket_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_ticket_messages';

        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE ticket_id = %d ORDER BY id ASC",
            $ticket_id
        ));

        foreach ($messages as $msg) {
            $sender = get_user_by('ID', $msg->sender_id);
            $msg->sender_name = $sender ? ($sender->display_name ?: $sender->user_login) : ($msg->is_admin ? 'پشتیبانی سایت' : 'کاربر');
            $msg->sender_avatar = $sender ? get_avatar_url($sender->ID, ['size' => 64]) : '';
        }

        return $messages;
    }

    public static function create_ticket($user_id, $subject, $department, $priority, $message, $attachment = '') {
        global $wpdb;
        $table_tickets = $wpdb->prefix . 'serene_tickets';
        $table_msgs    = $wpdb->prefix . 'serene_ticket_messages';

        $wpdb->insert($table_tickets, [
            'user_id'       => (int) $user_id,
            'department'    => sanitize_text_field($department),
            'priority'      => sanitize_text_field($priority),
            'subject'       => sanitize_text_field($subject),
            'status'        => 'open',
            'last_reply_by' => 'user',
            'updated_at'    => current_time('mysql'),
            'created_at'    => current_time('mysql'),
        ]);

        $ticket_id = $wpdb->insert_id;

        $wpdb->insert($table_msgs, [
            'ticket_id'      => $ticket_id,
            'sender_id'      => (int) $user_id,
            'is_admin'       => 0,
            'message'        => wp_kses_post($message),
            'attachment_url' => esc_url_raw($attachment),
            'created_at'     => current_time('mysql'),
        ]);

        return $ticket_id;
    }

    public static function reply_ticket($ticket_id, $sender_id, $message, $is_admin = 0, $attachment = '') {
        global $wpdb;
        $table_tickets = $wpdb->prefix . 'serene_tickets';
        $table_msgs    = $wpdb->prefix . 'serene_ticket_messages';

        $wpdb->insert($table_msgs, [
            'ticket_id'      => (int) $ticket_id,
            'sender_id'      => (int) $sender_id,
            'is_admin'       => (int) $is_admin,
            'message'        => wp_kses_post($message),
            'attachment_url' => esc_url_raw($attachment),
            'created_at'     => current_time('mysql'),
        ]);

        $new_status = $is_admin ? 'replied' : 'pending';
        $wpdb->update($table_tickets, [
            'status'        => $new_status,
            'last_reply_by' => $is_admin ? 'admin' : 'user',
            'updated_at'    => current_time('mysql'),
        ], ['id' => $ticket_id]);

        // If admin replied, send in-panel notification to user
        if ($is_admin) {
            $ticket = self::get_ticket($ticket_id);
            if ($ticket && class_exists('Serene_Panel_Notifications')) {
                Serene_Panel_Notifications::create_notification(
                    $ticket->user_id,
                    'پاسخ جدید به تیکت پشتیبانی #' . $ticket_id,
                    'تیکت شما با موضوع «' . $ticket->subject . '» توسط واحد پشتیبانی پاسخ داده شد.',
                    'ticket',
                    $ticket_id
                );
            }
        }

        return true;
    }

    public static function update_ticket_status($ticket_id, $status) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_tickets';
        return $wpdb->update($table, [
            'status'     => sanitize_text_field($status),
            'updated_at' => current_time('mysql'),
        ], ['id' => (int) $ticket_id]);
    }

    public static function update_ticket_priority($ticket_id, $priority) {
        global $wpdb;
        $table = $wpdb->prefix . 'serene_tickets';
        return $wpdb->update($table, [
            'priority'   => sanitize_text_field($priority),
            'updated_at' => current_time('mysql'),
        ], ['id' => (int) $ticket_id]);
    }

    public static function delete_ticket($ticket_id) {
        global $wpdb;
        $table_tickets = $wpdb->prefix . 'serene_tickets';
        $table_msgs    = $wpdb->prefix . 'serene_ticket_messages';

        $wpdb->delete($table_msgs, ['ticket_id' => (int) $ticket_id]);
        return $wpdb->delete($table_tickets, ['id' => (int) $ticket_id]);
    }
}
