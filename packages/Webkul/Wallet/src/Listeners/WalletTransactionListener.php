<?php

namespace Webkul\Wallet\Listeners;

use Bavix\Wallet\Internal\Events\TransactionCreatedEventInterface;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Support\Facades\Event;
use Webkul\Wallet\Events\WalletBalanceUpdated;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class WalletTransactionListener
{
    private const array REASON_MAP = [
        'wallet_topup'   => 'wallet_topup',
        'customer_topup' => 'wallet_topup',
        'wallet_payment' => 'wallet_spend',
        'admin_grant'    => 'admin_grant',
        'admin_deduct'   => 'admin_deduct',
        'wallet_refund'  => 'wallet_refund',
    ];

    public function handle(TransactionCreatedEventInterface $event): void
    {
        if (! core()->getConfigData('sales.wallet.events.publish_balance_updated')) {
            return;
        }

        $transaction = Transaction::with('payable')->find($event->getId());

        if (! $transaction) {
            return;
        }

        $holder = $transaction->payable;

        if (! $holder instanceof WalletCustomer) {
            return;
        }

        $metaType = $transaction->meta['type'] ?? null;
        $reason   = self::REASON_MAP[$metaType] ?? null;

        if (! $reason) {
            return;
        }

        $amountFloat = (float) $transaction->amountFloat;
        $newBalance  = $holder->balanceFloatNum;
        $oldBalance  = $newBalance - $amountFloat;

        Event::dispatch(new WalletBalanceUpdated(
            customerId:      $holder->id,
            oldBalance:      $oldBalance,
            newBalance:      $newBalance,
            reason:          $reason,
            customerGroupId: $holder->customer_group_id,
            transactionId:   $transaction->id,
        ));
    }
}
