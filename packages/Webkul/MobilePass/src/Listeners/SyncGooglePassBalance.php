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

        $googlePass = $this->mobilePassService->getCustomerGooglePass($event->customerId);

        if ($googlePass) {
            $this->mobilePassService->syncPassContent($googlePass, $event->customerId);
        }

        // Keep the Apple pass in step too. syncApplePassContent() no-ops when the
        // customer has no Apple pass, and updating the pass content triggers the
        // package's APNs push so an already-installed pass refreshes on-device.
        $this->mobilePassService->syncApplePassContent($event->customerId);
    }
}
