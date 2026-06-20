<?php

namespace Webkul\Wallet\Http\Controllers\Shop\Account;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Wallet\DataGrids\Shop\WalletTransactionDataGrid;
use Webkul\Wallet\Events\WalletBalanceUpdated;
use Webkul\Wallet\Models\Customer as WalletCustomer;
use Webkul\Wallet\Services\WalletService;

class WalletController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    public function index(): mixed
    {
        if (request()->ajax()) {
            return datagrid(WalletTransactionDataGrid::class)->process();
        }

        $customer = WalletCustomer::find(auth()->guard('customer')->id());

        return view('wallet::shop.customers.account.wallet.index', compact('customer'));
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

        $transaction = $customer->depositFloat($request->amount, [
            'type'         => 'customer_topup',
            'creator_type' => 'customer',
            'creator_id'   => $customer->id,
            'description'  => 'Manual top-up',
        ]);

        $newBalance = $customer->fresh()->balanceFloatNum;

        if (core()->getConfigData('sales.wallet.events.publish_balance_updated')) {
            Event::dispatch(new WalletBalanceUpdated(
                customerId: $customer->id,
                oldBalance: $oldBalance,
                newBalance: $newBalance,
                reason: 'wallet_topup',
                customerGroupId: $customer->customer_group_id,
                transactionId: $transaction->id,
            ));
        }

        return redirect()->route('shop.customers.account.wallet.index')
            ->with('success', trans('bagisto-wallet::app.customers.account.wallet.topup-success'));
    }
}
