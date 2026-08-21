<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Form_Builder {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function get_default_fields() {
        return [
            'national_code' => [
                'id'          => 'national_code',
                'label'       => 'کد ملی',
                'type'        => 'text',
                'enabled'     => 1,
                'required'    => 1,
                'placeholder' => '۰۰۱۲۳۴۵۶۷۸',
                'meta_key'    => '_serene_national_code',
            ],
            'birth_date' => [
                'id'          => 'birth_date',
                'label'       => 'تاریخ تولد (شمسی)',
                'type'        => 'text',
                'enabled'     => 1,
                'required'    => 0,
                'placeholder' => '۱۳۷۰/۰۱/۰۱',
                'meta_key'    => '_serene_birth_date',
            ],
            'sheba_number' => [
                'id'          => 'sheba_number',
                'label'       => 'شماره شبا (بدون IR)',
                'type'        => 'text',
                'enabled'     => 1,
                'required'    => 0,
                'placeholder' => '000000000000000000000000',
                'meta_key'    => '_serene_sheba',
            ],
            'job_title' => [
                'id'          => 'job_title',
                'label'       => 'شغل / زمینه فعالیت',
                'type'        => 'text',
                'enabled'     => 1,
                'required'    => 0,
                'placeholder' => 'طراح، برنامه‌نویس، مدیر...',
                'meta_key'    => '_serene_job_title',
            ],
        ];
    }

    public static function get_custom_fields() {
        $fields = get_option('serene_panel_custom_fields', null);
        if (!$fields || !is_array($fields) || empty($fields)) {
            return self::get_default_fields();
        }
        return $fields;
    }

    public static function get_active_fields() {
        $fields = self::get_custom_fields();
        $active = [];
        foreach ($fields as $key => $f) {
            if (!empty($f['enabled'])) {
                $active[$key] = $f;
            }
        }
        return $active;
    }
}
