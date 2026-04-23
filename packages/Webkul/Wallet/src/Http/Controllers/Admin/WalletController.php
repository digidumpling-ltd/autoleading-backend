<?php

namespace Webkul\Wallet\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class WalletController extends Controller
{
    public function index(int $id): View
    {
        abort_if(! bouncer()->hasPermission('customers.wallet'), 401);

        $customer = WalletCustomer::findOrFail($id);

        $transactions = $customer->transactions()->latest()->paginate(20);

        return view('wallet::admin.customers.wallet.index', compact('customer', 'transactions'));
    }

    public function adjust(Request $request, int $id): RedirectResponse
    {
        abort_if(! bouncer()->hasPermission('customers.wallet'), 401);

        $request->validate([
            'type'   => 'required|in:add,deduct',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|min:5',
        ]);

        $customer = WalletCustomer::findOrFail($id);

        if ($request->type === 'add') {
            $customer->depositFloat($request->amount, [
                'type'     => 'admin_grant',
                'admin_id' => auth()->guard('admin')->id(),
                'reason'   => $request->reason,
            ]);

            return redirect()
                ->route('admin.customers.wallet.index', $id)
                ->with('success', trans('bagisto-wallet::app.admin.customers.wallet.adjust-add-success'));
        }

        if (! $customer->canWithdrawFloat($request->amount)) {
            return back()->withErrors([
                'amount' => trans('bagisto-wallet::app.admin.customers.wallet.insufficient-balance'),
            ]);
        }

        $customer->withdrawFloat($request->amount, [
            'type'     => 'admin_deduct',
            'admin_id' => auth()->guard('admin')->id(),
            'reason'   => $request->reason,
        ]);

        return redirect()
            ->route('admin.customers.wallet.index', $id)
            ->with('success', trans('bagisto-wallet::app.admin.customers.wallet.adjust-deduct-success'));
    }
}
