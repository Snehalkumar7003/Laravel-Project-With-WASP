<?php
namespace App\Mail;
class ForgotPasswordMail extends BaseMail{
    /**
     * Create a new message instance.
     */
    public function __construct(object $user,string $token) {
        parent::__construct(
            subject: 'Reset Your Password',
            view: 'emails.forgot-password',
            data: [
                'title' => 'Forgot Password',
                'user' => $user,
                'resetLink' => route(
                    'reset-password',
                    ['token' => $token]
                ),
                'expiry' => 10,
                'application' => config('app.name')
            ]
        );
    }
}