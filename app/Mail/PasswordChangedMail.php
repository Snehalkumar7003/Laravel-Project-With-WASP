<?php

namespace App\Mail;

class PasswordChangedMail extends BaseMail{
    /**
     * Create a new message instance.
     */
    public function __construct(
        object $user,
        string $ipAddress,
        string $userAgent
    ) {

        parent::__construct(
            subject: 'Password Changed Successfully',
            view: 'emails.password-changed',
            data: [
                'title'       => 'Password Changed Successfully',
                'user'        => $user,
                'ipAddress'   => $ipAddress,
                'userAgent'   => $userAgent,
                'changedAt'   => now(),
                'application' => config('app.name'),
            ]

        );

    }
}