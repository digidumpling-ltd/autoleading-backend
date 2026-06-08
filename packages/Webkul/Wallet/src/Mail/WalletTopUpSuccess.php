<?php

namespace Webkul\Wallet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WalletTopUpSuccess extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $data) {}

    public function build(): static
    {
        return $this
            ->from(core()->getSenderEmailDetails()['email'], core()->getSenderEmailDetails()['name'])
            ->to($this->data['email'])
            ->subject(trans('bagisto-wallet::app.mail.topup-success.subject'))
            ->view('wallet::emails.wallet.topup-success');
    }
}
