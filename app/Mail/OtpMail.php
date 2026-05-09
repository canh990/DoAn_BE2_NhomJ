<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $userName;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $userName, $type = 'forgot_password')
    {
        $this->otp = $otp;
        $this->userName = $userName;
        $this->type = $type;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        if ($this->type === 'register') {
            $subject = 'Mã xác thực đăng ký tài khoản - NHOMJ';
        } elseif ($this->type === 'account_action') {
            $subject = 'Mã xác thực bảo mật tài khoản - NHOMJ';
        } else {
            $subject = 'Mã xác thực khôi phục mật khẩu - NHOMJ';
        }

        return $this->subject($subject)
                    ->view('emails.otp');
    }
}