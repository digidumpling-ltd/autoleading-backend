<?php

namespace Webkul\Wallet\Listeners;

use Webkul\Wallet\Events\WalletBalanceUpdated;
use Webkul\Wallet\Models\Customer as WalletCustomer;
use Webkul\Wallet\Services\WalletService;

class WalletNotificationListener
{
    public function __construct(protected WalletService $walletService) {}

    public function handle(WalletBalanceUpdated $event): void
    {
        if (! core()->getConfigData('sales.wallet.notifications.topup_email_enabled')) {
            return;
        }

        $customer = WalletCustomer::find($event->customerId);

        if (! $customer) {
            return;
        }

        $amount = abs($event->newBalance - $event->oldBalance);

        match ($event->reason) {
            'wallet_topup' => $this->walletService->notifyTopUp($customer, $amount, $event->newBalance),
            'wallet_reward' => $this->walletService->notifyReward($customer, $amount),
            default         => null,
        };
    }
}
