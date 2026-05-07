<?php

namespace Webkul\CustomerVerification\Listeners;

use Illuminate\Support\Facades\Auth;
use Webkul\CustomerVerification\Support\Verification;

class PreventUnverifiedAddToCartListener
{
    public function handle(mixed $_product): void
    {
        if (! core()->getConfigData('customer_verification.checkout.gating.require_verification')) {
            return;
        }

        if (! Auth::guard('customer')->check()) {
            return;
        }

        $customer = Auth::guard('customer')->user();

        if ($customer->verification_status !== Verification::STATUS_APPROVED) {
            throw new \Exception(
                strip_tags(trans('customer-verification::app.common.cannot_add_unverified', [
                    'dashboard_url' => route('shop.customer.verification.index'),
                ]))
            );
        }
    }
}
