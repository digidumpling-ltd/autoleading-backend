<?php

namespace Webkul\Wallet\Listeners;

use Illuminate\Support\Facades\Event;
use Webkul\Sales\Repositories\OrderTransactionRepository;
use Webkul\Wallet\Events\WalletBalanceUpdated;
use Webkul\Wallet\Models\Channel as WalletChannel;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class WalletInvoiceListener
{
    public function __construct(
        protected OrderTransactionRepository $orderTransactionRepository
    ) {}

    public function handle($invoice): void
    {
        if ($invoice->order->payment->method !== 'wallet') {
            return;
        }

        $alreadyProcessed = $this->orderTransactionRepository
            ->findWhere([
                'invoice_id'     => $invoice->id,
                'payment_method' => 'wallet',
            ])->isNotEmpty();

        if ($alreadyProcessed) {
            return;
        }

        $customer = WalletCustomer::find($invoice->order->customer_id);

        if (! $customer) {
            throw new \Exception('Wallet customer not found for order #' . $invoice->order_id);
        }

        $amount = (float) $invoice->base_grand_total;

        if (! $customer->canWithdrawFloat($amount)) {
            throw new \Exception(
                trans('bagisto-wallet::app.listeners.wallet-invoice.insufficient-balance')
            );
        }

        $channel = WalletChannel::find($invoice->order->channel_id);

        $oldBalance = $customer->balanceFloatNum;

        $customer->transferFloat($channel, $amount, [
            'type'        => 'wallet_payment',
            'order_id'    => $invoice->order_id,
            'invoice_id'  => $invoice->id,
            'description' => trans('bagisto-wallet::app.listeners.wallet-invoice.description', ['order' => $invoice->order_id]),
        ]);

        if (core()->getConfigData('sales.wallet.events.publish_balance_updated')) {
            Event::dispatch(new WalletBalanceUpdated(
                customerId: $customer->id,
                oldBalance: $oldBalance,
                newBalance: $customer->fresh()->balanceFloatNum,
                reason: 'wallet_spend',
                customerGroupId: $customer->customer_group_id,
            ));
        }

        $this->orderTransactionRepository->create([
            'transaction_id' => 'wallet_tx_' . $invoice->id . '_' . uniqid(),
            'status'         => 'paid',
            'type'           => 'capture',
            'amount'         => $invoice->base_grand_total,
            'payment_method' => 'wallet',
            'invoice_id'     => $invoice->id,
            'order_id'       => $invoice->order_id,
        ]);
    }
}
