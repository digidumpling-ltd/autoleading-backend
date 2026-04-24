<?php

namespace Webkul\Wallet\Http\Controllers\Shop\Checkout;

use Illuminate\Http\JsonResponse;
use Webkul\Checkout\Facades\Cart;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Wallet\Models\Customer as WalletCustomer;
use Webkul\Wallet\Services\WalletService;

class WalletCheckoutController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Returns wallet balance status for the current cart — used by the checkout UI.
     */
    public function status(): JsonResponse
    {
        if (! auth('customer')->check()) {
            return response()->json(['can_afford' => false, 'balance' => 0, 'shortfall' => 0]);
        }

        $customer = WalletCustomer::find(auth('customer')->id());

        if (! $customer) {
            return response()->json(['can_afford' => false, 'balance' => 0, 'shortfall' => 0]);
        }

        $cart = Cart::getCart();
        $amount = $cart ? (float) $cart->grand_total : 0.0;

        return response()->json([
            'can_afford'       => $this->walletService->canAfford($customer, $amount),
            'balance'          => $customer->balanceFloatNum,
            'formatted_balance' => core()->currency($customer->balanceFloatNum),
            'shortfall'        => $this->walletService->shortfall($customer, $amount),
            'wallet_url'       => route('shop.customers.account.wallet.index'),
        ]);
    }
}
