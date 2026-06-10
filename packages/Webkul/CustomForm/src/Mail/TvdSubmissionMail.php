<?php

namespace Webkul\CustomForm\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Webkul\CustomForm\Models\TvdSubmission;

class TvdSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TvdSubmission $submission
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                core()->getSenderEmailDetails()['email'],
                core()->getSenderEmailDetails()['name']
            ),
            to: [
                new Address(
                    core()->getAdminEmailDetails()['email'],
                    core()->getAdminEmailDetails()['name']
                ),
            ],
            subject: trans('customform::app.mail-subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'customform::mail.tvd-submission',
        );
    }
}
