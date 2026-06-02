<?php

namespace Themes\CustomTheme\Mail\Order;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Webkul\Sales\Contracts\Order as OrderContract;
use Webkul\Shop\Mail\Mailable;

class OrderConfirmedNotification extends Mailable
{
    public function __construct(public OrderContract $order)
    {
        $this->locale(
            $order->items->first()?->additional['locale'] ?? 'en'
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [new Address(
                $this->order->customer_email,
                $this->order->customer_full_name,
            )],
            subject: trans('custom-theme::app.emails.orders.order-confirmed.subject', [
                'order_id' => $this->order->increment_id,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'custom-theme::emails.orders.order-confirmed',
            with: ['order' => $this->order],
        );
    }
}
