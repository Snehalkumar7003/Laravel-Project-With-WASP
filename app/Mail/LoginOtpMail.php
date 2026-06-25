<?php

namespace App\Mail;

class LoginOtpMail extends BaseMail
{
    /**
     * Create a new message instance.
     */
    public function __construct(object $user,string $otp) {

        parent::__construct(

            subject: 'Your Login Verification Code',
            view: 'emails.otp',
            data: [
                'title' => 'Login Verification',
                'user' => $user,
                'otp' => $otp,
                'expiry' => 5,
                'application' => config('app.name')
            ]
        );

    }
}