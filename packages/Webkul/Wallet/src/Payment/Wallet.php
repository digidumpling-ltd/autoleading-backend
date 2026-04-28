<?php

namespace Webkul\Wallet\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Checkout\Facades\Cart;
use Webkul\Payment\Payment\Payment;
use Webkul\Wallet\Models\Customer as WalletCustomer;
use Webkul\Wallet\Services\WalletService;

class Wallet extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code = 'wallet';

    public function getImage(): string
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : '';
    }

    /**
     * Redirect to wallet top-up page when balance is insufficient, null otherwise.
     */
    public function getRedirectUrl(): ?string
    {
        if (! auth('customer')->check()) {
            return null;
        }

        $cart = Cart::getCart();

        if (! $cart) {
            return null;
        }

        $customer = WalletCustomer::find(auth('customer')->id());

        if (! $customer) {
            return null;
        }

        $service = app(WalletService::class);

        if (! $service->canAfford($customer, (float) $cart->grand_total)) {
            $shortfall = $service->shortfall($customer, (float) $cart->grand_total);

            return route('shop.customers.account.wallet.index')
                . '?reason=insufficient_balance&required=' . $shortfall;
        }

        return null;
    }

    /**
     * Check if payment method is available.
     */
    public function isAvailable(): bool
    {
        if (! $this->getConfigData('active')) {
            return false;
        }

        return auth('customer')->check();
    }
}
