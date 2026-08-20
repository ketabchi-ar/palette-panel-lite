<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_WebAuthn {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function get_register_options($user_id) {
        $user = get_user_by('ID', $user_id);
        if (!$user) {
            return new WP_Error('user_not_found', 'کاربر یافت نشد.');
        }

        $challenge = bin2hex(random_bytes(32));
        set_transient('serene_webauthn_reg_' . $user_id, $challenge, 300);

        return [
            'challenge' => $challenge,
            'rp'        => [
                'name' => get_bloginfo('name'),
                'id'   => parse_url(home_url(), PHP_URL_HOST),
            ],
            'user'      => [
                'id'          => base64_encode((string) $user_id),
                'name'        => $user->user_login,
                'displayName' => $user->display_name ?: $user->user_login,
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7], // ES256
                ['type' => 'public-key', 'alg' => -257], // RS256
            ],
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform',
                'userVerification'        => 'preferred',
            ],
            'timeout' => 60000,
        ];
    }

    public static function save_registered_key($user_id, $cred_id, $public_key) {
        $keys = get_user_meta($user_id, '_serene_webauthn_keys', true) ?: [];
        $keys[] = [
            'id'         => sanitize_text_field($cred_id),
            'publicKey'  => sanitize_text_field($public_key),
            'name'       => 'اثر انگشت / کلید بیومتریک',
            'created_at' => current_time('mysql'),
        ];
        update_user_meta($user_id, '_serene_webauthn_keys', $keys);
        return true;
    }

    public static function get_login_options($user_id = 0) {
        $challenge = bin2hex(random_bytes(32));
        set_transient('serene_webauthn_login_challenge', $challenge, 300);

        return [
            'challenge' => $challenge,
            'rpId'      => parse_url(home_url(), PHP_URL_HOST),
            'timeout'   => 60000,
            'userVerification' => 'preferred',
        ];
    }
}
