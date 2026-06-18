<?php

namespace Webkul\MobilePass\Listeners;

use Webkul\MobilePass\Services\MobilePassService;
use Webkul\Wallet\Events\WalletBalanceUpdated;

class SyncGooglePassBalance
{
    public function __construct(protected MobilePassService $mobilePassService) {}

    public function handle(WalletBalanceUpdated $event): void
    {
        if (! $this->mobilePassService->isEnabled()) {
            return;
        }

        $pass = $this->mobilePassService->getCustomerGooglePass($event->customerId);

        if (! $pass) {
            return;
        }

        $this->mobilePassService->syncPassContent($pass, $event->customerId);
    }
}
