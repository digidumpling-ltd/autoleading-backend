<?php

namespace Webkul\CustomerVerification\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Webkul\CustomerVerification\Support\Verification;

class VerificationTopUpMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        // Only act on wallet top-up routes
        if (! $request->routeIs('shop.customers.account.wallet.topup') && ! $request->routeIs('shop.customers.account.wallet.topup.store')) {
            return $next($request);
        }

        if (! core()->getConfigData('customer_verification.wallet.settings.require_verification')) {
            return $next($request);
        }

        $customer = auth('customer')->user();

        if (! $customer) {
            return $next($request);
        }

        if ($customer->verification_status !== Verification::STATUS_APPROVED) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => trans('customer-verification::app.common.wallet_topup_requires_verification'),
                    'redirect'     => true,
                    'redirect_url' => route('shop.customer.verification.index'),
                ], 403);
            }

            return redirect()->route('shop.customer.verification.index')
                ->with('error', trans('customer-verification::app.common.wallet_topup_requires_verification'));
        }

        return $next($request);
    }
}
