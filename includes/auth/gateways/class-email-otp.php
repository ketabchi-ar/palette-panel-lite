<?php
if (!defined('ABSPATH')) {
    exit;
}

class Serene_Gateway_Email {
    public static function send_otp($email, $code) {
        $blogname = get_bloginfo('name');
        $subject  = sprintf('[%s] کد تایید ورود و ثبت‌نام', $blogname);
        
        $message = '
        <div dir="rtl" style="font-family: Tahoma, Arial, sans-serif; background-color: #f9f9fe; padding: 30px; text-align: center; color: #2f323b;">
            <div style="max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #dfe2ed;">
                <div style="background: #b6c8fc; width: 60px; height: 60px; border-radius: 50%; margin: 0 auto 20px; line-height: 60px; font-size: 28px;">🔐</div>
                <h2 style="color: #4c5e8b; margin-top: 0;">' . esc_html($blogname) . '</h2>
                <p style="font-size: 15px; color: #5b5f68;">کد یکبار مصرف ورود به حساب کاربری شما:</p>
                <div style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #4c5e8b; background: #f2f3fb; padding: 15px; border-radius: 12px; margin: 25px 0; border: 1px dashed #4c5e8b;">
                    ' . esc_html($code) . '
                </div>
                <p style="font-size: 13px; color: #aeb2bc;">این کد برای ۲ دقیقه آینده معتبر است. در صورتی که شما درخواست ورود نداده‌اید، این پیام را نادیده بگیرید.</p>
            </div>
        </div>';

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $blogname . ' <' . get_option('admin_email') . '>'
        ];

        return wp_mail($email, $subject, $message, $headers);
    }
}
