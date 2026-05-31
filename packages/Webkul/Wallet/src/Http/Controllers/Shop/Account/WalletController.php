<?php

namespace Webkul\Wallet\Http\Controllers\Shop\Account;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Wallet\Events\WalletBalanceUpdated;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class WalletController extends Controller
{
    public function index(): View
    {
        $customer = WalletCustomer::find(auth()->guard('customer')->id());

        $transactions = $customer->transactions()->latest()->paginate(15);

        return view('wallet::shop.customers.account.wallet.index', compact('customer', 'transactions'));
    }

    public function topup(): View
    {
        $customer = WalletCustomer::find(auth()->guard('customer')->id());

        return view('wallet::shop.customers.account.wallet.topup', compact('customer'));
    }

    public function processTopup(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $customer = WalletCustomer::find(auth()->guard('customer')->id());

        $oldBalance = $customer->balanceFloatNum;

        $customer->depositFloat($request->amount, ['description' => 'Manual top-up']);

        if (core()->getConfigData('sales.wallet.events.publish_balance_updated')) {
            Event::dispatch(new WalletBalanceUpdated(
                customerId: $customer->id,
                oldBalance: $oldBalance,
                newBalance: $customer->fresh()->balanceFloatNum,
                reason: 'topup',
                customerGroupId: $customer->customer_group_id,
            ));
        }

        return redirect()->route('shop.customers.account.wallet.index')
            ->with('success', trans('bagisto-wallet::app.customers.account.wallet.topup-success'));
    }
}
