<?php

namespace Webkul\Wallet\Listeners;

use Webkul\Wallet\Models\Channel as WalletChannel;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class WalletRefundListener
{
    public function handle($refund): void
    {
        if ($refund->order->payment->method !== 'wallet') {
            return;
        }

        $customer = WalletCustomer::find($refund->order->customer_id);

        if (! $customer) {
            return;
        }

        $alreadyProcessed = $customer->transactions()
            ->where('type', 'deposit')
            ->where('meta->type', 'wallet_refund')
            ->where('meta->refund_id', $refund->id)
            ->exists();

        if ($alreadyProcessed) {
            return;
        }

        $amount = (float) $refund->base_grand_total;

        if ($amount <= 0.0) {
            return;
        }

        $channel = WalletChannel::find($refund->order->channel_id);

        $channel->forceTransferFloat($customer, $amount, [
            'type'        => 'wallet_refund',
            'order_id'    => $refund->order_id,
            'refund_id'   => $refund->id,
            'description' => trans('bagisto-wallet::app.listeners.wallet-refund.description', ['order' => $refund->order_id]),
        ]);
    }
}
