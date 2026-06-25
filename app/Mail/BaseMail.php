<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

abstract class BaseMail extends Mailable
{
    use SerializesModels;

    /**
     * Mail Subject
     */
    protected string $mailSubject;

    /**
     * Blade View
     */
    protected string $viewName;

    /**
     * Mail Data
     */
    protected array $mailData = [];

    /**
     * Create a new mail instance.
     */
    public function __construct(
        string $subject,
        string $view,
        array $data = []
    ) {
        $this->mailSubject = $subject;
        $this->viewName    = $view;
        $this->mailData    = $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Envelope
    |--------------------------------------------------------------------------
    */

    public function envelope(): Envelope
    {
        return new Envelope(

            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),

            subject: $this->mailSubject

        );
    }

    /*
    |--------------------------------------------------------------------------
    | Content
    |--------------------------------------------------------------------------
    */

    public function content(): Content
    {
        return new Content(

            view: $this->viewName,

            with: $this->mailData

        );
    }

    /*
    |--------------------------------------------------------------------------
    | Attachments
    |--------------------------------------------------------------------------
    */

    public function attachments(): array
    {
        return [];
    }
}