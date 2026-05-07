<?php

namespace Webkul\CustomerVerification\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Webkul\CustomerVerification\Support\Verification;

class VerificationCheckoutMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! $request->routeIs('shop.checkout.onepage.orders.store')) {
            return $next($request);
        }

        if (! core()->getConfigData('customer_verification.checkout.gating.require_verification')) {
            return $next($request);
        }

        $customer = auth('customer')->user();

        if (! $customer) {
            return $next($request);
        }

        if ($customer->verification_status !== Verification::STATUS_APPROVED) {
            return response()->json([
                'message'      => trans('customer-verification::app.common.cannot_checkout_unverified'),
                'redirect'     => true,
                'redirect_url' => route('shop.customer.verification.index'),
            ], 403);
        }

        return $next($request);
    }
}
