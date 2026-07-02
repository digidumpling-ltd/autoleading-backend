<?php

namespace Webkul\CustomerVerification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Webkul\Customer\Models\Customer;

class ReminderNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [
                new Address($this->customer->email, $this->customer->name),
            ],
            subject: trans('customer-verification::app.emails.reminder.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'customer-verification::emails.reminder',
        );
    }

    protected function buildFrom($message): static
    {
        ! empty($this->from)
            ? $message->from($this->from[0]['address'], $this->from[0]['name'])
            : $message->from(core()->getSenderEmailDetails()['email'], core()->getSenderEmailDetails()['name']);

        return $this;
    }
}
