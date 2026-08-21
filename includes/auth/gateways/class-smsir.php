<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-sms-gateway.php';

class Serene_Gateway_SmsIr implements Serene_SMS_Gateway_Interface {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = trim($api_key);
    }

    public function send_otp($phone, $code, $pattern = '') {
        $url = 'https://api.sms.ir/v1/send/verify';
        
        // Normalize mobile phone
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 2) === '98') {
            $phone = '0' . substr($phone, 2);
        }

        $opt = get_option('serene_panel_options', []);
        $template_id = !empty($pattern) ? (int) $pattern : (int) ($opt['smsir_template_id'] ?? 0);
        $param_name = !empty($opt['smsir_param_name']) ? trim($opt['smsir_param_name']) : 'Code';

        // SMS.ir requires strict 1-to-1 parameter matching defined in the template
        $parameters = [
            [
                'name'  => $param_name,
                'value' => (string) $code
            ]
        ];

        $response = wp_remote_post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'x-api-key'    => $this->api_key,
            ],
            'timeout' => 20,
            'body'    => wp_json_encode([
                'mobile'     => $phone,
                'templateId' => $template_id,
                'parameters' => $parameters,
            ])
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $raw_body = wp_remote_retrieve_body($response);
        $body = json_decode($raw_body, true);

        if ($status_code === 401 || $status_code === 403) {
            return new WP_Error('smsir_auth_error', 'کلید وب‌سرویس SMS.ir (x-api-key) نامعتبر است. لطفاً کلید API را از منوی توسعه‌دهندگان پنل SMS.ir مجدداً بررسی فرمایید.');
        }

        if ($status_code === 400) {
            $msg = $body['message'] ?? 'شناسه قالب (Template ID) یا نام متغیر پارامتر در SMS.ir نادرست است.';
            return new WP_Error('smsir_bad_request', 'خطای SMS.ir: ' . $msg . " (متغیر ارسالی: {$param_name})");
        }

        if (isset($body['status']) && ($body['status'] == 1 || $body['status'] === 200)) {
            return true;
        }

        $err_msg = isset($body['message']) ? $body['message'] : 'خطای نامشخص در وب‌سرویس SMS.ir';
        return new WP_Error('smsir_error', $err_msg);
    }

    public function send_custom_sms($phone, $message) {
        $url = 'https://api.sms.ir/v1/send/bulk';
        $opt = get_option('serene_panel_options', []);
        $linenumber = $opt['smsir_linenumber'] ?? '';

        $response = wp_remote_post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'x-api-key'    => $this->api_key,
            ],
            'timeout' => 20,
            'body'    => wp_json_encode([
                'lineNumber' => (int) $linenumber,
                'MessageText'=> (string) $message,
                'Mobiles'    => [(string) $phone],
            ])
        ]);

        if (is_wp_error($response)) return $response;
        $body = json_decode(wp_remote_retrieve_body($response), true);
        return isset($body['status']) && $body['status'] == 1;
    }

    public function send_voice_call($phone, $code) {
        return new WP_Error('not_supported', 'تماس صوتی در وب‌سرویس SMS.ir پشتیبانی نمی‌شود.');
    }
}
