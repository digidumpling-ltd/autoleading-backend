<?php

namespace Webkul\Wallet\Http\Controllers\Shop;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Wallet\Contracts\SupportsWalletTopUp;
use Webkul\Wallet\Models\Customer as WalletCustomer;
use Webkul\Wallet\Models\WalletTopUp;

class WalletTopUpController extends Controller
{
    public function create(): View
    {
        $customer = WalletCustomer::find(auth()->guard('customer')->id());

        $methods = $this->resolveTopUpMethods();

        return view('wallet::shop.customers.account.wallet.topup', compact('customer', 'methods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string|not_in:wallet',
        ]);

        $methods      = $this->resolveTopUpMethods();
        $methodConfig = $methods->firstWhere('method', $validated['payment_method']);

        if (! $methodConfig) {
            return back()->withErrors(['payment_method' => trans('bagisto-wallet::app.customers.account.wallet.topup-invalid-method')]);
        }

        $topUp = WalletTopUp::create([
            'customer_id'    => auth()->guard('customer')->id(),
            'amount'         => (float) $validated['amount'],
            'currency'       => core()->getCurrentCurrencyCode(),
            'payment_method' => $validated['payment_method'],
            'status'         => WalletTopUp::STATUS_PENDING,
        ]);

        session(['wallet_topup_id' => $topUp->id]);

        return redirect($methodConfig['topup_redirect_url']);
    }

    /**
     * Build the list of payment methods available for wallet top-up.
     *
     * Reads from the admin-configured topup_allowed_methods. Ignores the
     * storefront active flag — a method can be disabled for checkout but
     * still enabled for top-ups via the wallet settings.
     */
    protected function resolveTopUpMethods(): \Illuminate\Support\Collection
    {
        $allowed = $this->allowedMethodCodes();

        return collect(config('payment_methods', []))
            ->filter(function (array $config) use ($allowed) {
                $code = $config['code'] ?? '';

                if ($code === 'wallet') {
                    return false;
                }

                if ($allowed !== null && ! in_array($code, $allowed)) {
                    return false;
                }

                $class = app($config['class']);

                return $class instanceof SupportsWalletTopUp;
            })
            ->map(function (array $config) {
                $class = app($config['class']);

                return [
                    'method'           => $config['code'],
                    'method_title'     => $class->getTitle(),
                    'topup_redirect_url' => $class->getTopUpRedirectUrl(),
                ];
            })
            ->values();
    }

    protected function allowedMethodCodes(): ?array
    {
        $value = core()->getConfigData('sales.wallet.settings.topup_allowed_methods');

        if (empty($value)) {
            return null;
        }

        return array_filter(array_map('trim', explode(',', $value)));
    }
}
