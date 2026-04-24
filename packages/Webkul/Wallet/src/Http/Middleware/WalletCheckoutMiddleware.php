<?php

namespace Webkul\Wallet\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;
use Webkul\Wallet\Models\Customer as WalletCustomer;
use Webkul\Wallet\Services\WalletService;

class WalletCheckoutMiddleware
{
    public function __construct(protected WalletService $walletService) {}

    public function handle(Request $request, Closure $next): mixed
    {
        if (! $request->routeIs('shop.checkout.onepage.orders.store')) {
            return $next($request);
        }

        $cart = Cart::getCart();

        if (! $cart || $cart->payment?->method !== 'wallet') {
            return $next($request);
        }

        $customer = WalletCustomer::find(auth('customer')->id());

        if (! $customer) {
            return $next($request);
        }

        if (! $this->walletService->canAfford($customer, (float) $cart->grand_total)) {
            return response()->json([
                'message' => trans('bagisto-wallet::app.checkout.insufficient-balance-server', [
                    'required'  => core()->currency($cart->grand_total),
                    'available' => core()->currency($customer->balanceFloatNum),
                ]),
            ], 500);
        }

        return $next($request);
    }
}
