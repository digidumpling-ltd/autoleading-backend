<?php

namespace Webkul\CustomerVerification\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Webkul\CustomerVerification\Support\Verification;

class PreventUnverifiedRentalAddToCartListener implements ShouldQueue
{
    public function handle($event): void
    {
        // Only check for authenticated customers
        if (! Auth::guard('customer')->check()) {
            return;
        }

        $product = $event->product ?? null;

        // Check if product requires verification (has rental flag/attribute)
        if (! $this->isRentalProduct($product)) {
            return;
        }

        $customer = Auth::guard('customer')->user();

        // Check customer's verification status
        if ($customer->verification_status !== Verification::STATUS_APPROVED) {
            throw new \Exception(
                trans('shop::app.customers.verification.cannot-add-rental-unverified', [
                    'dashboard_url' => route('shop.customer.verification.index'),
                ])
            );
        }
    }

    private function isRentalProduct($product): bool
    {
        if (! $product) {
            return false;
        }

        // Check product type is 'rental' or has a rental attribute
        if ($product->type === 'rental') {
            return true;
        }

        // Alternative: check for a custom attribute
        if (method_exists($product, 'getAttribute')) {
            return (bool) $product->getAttribute('requires_verification');
        }

        return false;
    }
}
