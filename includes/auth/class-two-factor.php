<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Panel_Two_Factor {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public static function generate_secret() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    public static function get_qr_code_url($user_id, $secret) {
        $user = get_user_by('ID', $user_id);
        $issuer = rawurlencode(get_bloginfo('name'));
        $account = rawurlencode($user->user_email ?: $user->user_login);
        
        $otpauth = "otpauth://totp/{$issuer}:{$account}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($otpauth);
    }

    public static function verify_totp($secret, $code, $tolerance = 1) {
        $base32 = new class {
            public function decode($secret) {
                $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
                $base32charsFlipped = array_flip(str_split($base32chars));
                $paddingCharCount = substr_count($secret, '=');
                $allowedValues = [6, 4, 3, 1, 0];
                if (!in_array($paddingCharCount, $allowedValues)) return false;
                for ($i = 0; $i < 4; $i++) {
                    if ($paddingCharCount == $allowedValues[$i] &&
                        substr($secret, -($allowedValues[$i])) != str_repeat('=', $allowedValues[$i])) return false;
                }
                $secret = str_replace('=', '', $secret);
                $secret = str_split($secret);
                $binaryString = '';
                for ($i = 0; $i < count($secret); $i = $i + 8) {
                    $x = '';
                    if (!in_array($secret[$i], str_split($base32chars))) return false;
                    for ($j = 0; $j < 8; $j++) {
                        $x .= str_pad(base_convert(@$base32charsFlipped[$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
                    }
                    $eightBits = str_split($x, 8);
                    for ($z = 0; $z < count($eightBits); $z++) {
                        $binaryString .= (($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48) ? $y : '';
                    }
                }
                return $binaryString;
            }
        };

        $key = $base32->decode($secret);
        if (!$key) return false;

        $timeSlice = floor(time() / 30);
        for ($i = -$tolerance; $i <= $tolerance; $i++) {
            $calculatedCode = self::get_code($key, $timeSlice + $i);
            if (hash_equals((string)$calculatedCode, (string)$code)) {
                return true;
            }
        }
        return false;
    }

    private static function get_code($secret, $timeSlice) {
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hmac = hash_hmac('sha1', $time, $secret, true);
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashpart = substr($hmac, $offset, 4);
        $value = unpack('N', $hashpart);
        $value = $value[1];
        $value = $value & 0x7FFFFFFF;
        return str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
    }
}
