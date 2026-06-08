<?php

namespace Webkul\Wallet\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WalletTopUpGatingMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! $request->routeIs('shop.customers.account.wallet.topup') && ! $request->routeIs('shop.customers.account.wallet.topup.store')) {
            return $next($request);
        }

        if (! core()->getConfigData('sales.wallet.gating.require_topup_verification')) {
            return $next($request);
        }

        $customer = auth('customer')->user();

        if (! $customer) {
            return $next($request);
        }

        if (($customer->verification_status ?? 'incomplete') !== 'approved') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => trans('bagisto-wallet::app.common.topup-requires-verification'),
                    'redirect'     => true,
                    'redirect_url' => route('shop.customer.verification.index'),
                ], 403);
            }

            return redirect()->route('shop.customer.verification.index')
                ->with('error', trans('bagisto-wallet::app.common.topup-requires-verification'));
        }

        return $next($request);
    }
}
