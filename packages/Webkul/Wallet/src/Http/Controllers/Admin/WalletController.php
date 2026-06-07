<?php

namespace Webkul\Wallet\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Wallet\DataGrids\Admin\WalletTransactionDataGrid;
use Webkul\Wallet\Events\WalletBalanceUpdated;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class WalletController extends Controller
{
    public function index(int $id): View
    {
        abort_if(! bouncer()->hasPermission('customers.wallet'), 401);

        $customer = WalletCustomer::findOrFail($id);

        return view('wallet::admin.customers.wallet.index', compact('customer'));
    }

    public function transactions(int $id): JsonResponse
    {
        abort_if(! bouncer()->hasPermission('customers.wallet'), 401);

        return datagrid(WalletTransactionDataGrid::class)->process();
    }

    public function balance(int $id): JsonResponse
    {
        abort_if(! bouncer()->hasPermission('customers.wallet'), 401);

        $customer = WalletCustomer::findOrFail($id);

        return response()->json([
            'balance' => core()->formatPrice($customer->balanceFloatNum),
        ]);
    }

    public function ajaxAdjust(Request $request, int $id): JsonResponse
    {
        abort_if(! bouncer()->hasPermission('customers.wallet'), 401);

        $validated = $request->validate([
            'type'   => 'required|in:add,deduct',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|min:5',
        ]);

        $customer = WalletCustomer::findOrFail($id);

        if ($validated['type'] === 'add') {
            $oldBalance = $customer->balanceFloatNum;

            $customer->depositFloat($validated['amount'], [
                'type'     => 'admin_grant',
                'admin_id' => auth()->guard('admin')->id(),
                'reason'   => $validated['reason'],
            ]);

            if (core()->getConfigData('sales.wallet.events.publish_balance_updated')) {
                Event::dispatch(new WalletBalanceUpdated(
                    customerId: $customer->id,
                    oldBalance: $oldBalance,
                    newBalance: $customer->fresh()->balanceFloatNum,
                    reason: 'admin_grant',
                    customerGroupId: $customer->customer_group_id,
                ));
            }

            return response()->json([
                'message' => trans('bagisto-wallet::app.admin.customers.wallet.adjust-add-success'),
                'balance' => core()->formatPrice($customer->fresh()->balanceFloatNum),
            ]);
        }

        if (! $customer->canWithdrawFloat($validated['amount'])) {
            return response()->json([
                'errors' => [
                    'amount' => [trans('bagisto-wallet::app.admin.customers.wallet.insufficient-balance')],
                ],
            ], 422);
        }

        $oldBalance = $customer->balanceFloatNum;

        $customer->withdrawFloat($validated['amount'], [
            'type'     => 'admin_deduct',
            'admin_id' => auth()->guard('admin')->id(),
            'reason'   => $validated['reason'],
        ]);

        if (core()->getConfigData('sales.wallet.events.publish_balance_updated')) {
            Event::dispatch(new WalletBalanceUpdated(
                customerId: $customer->id,
                oldBalance: $oldBalance,
                newBalance: $customer->fresh()->balanceFloatNum,
                reason: 'admin_deduct',
            ));
        }

        return response()->json([
            'message' => trans('bagisto-wallet::app.admin.customers.wallet.adjust-deduct-success'),
            'balance' => core()->formatPrice($customer->fresh()->balanceFloatNum),
        ]);
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
            $oldBalance = $customer->balanceFloatNum;

            $customer->depositFloat($request->amount, [
                'type'     => 'admin_grant',
                'admin_id' => auth()->guard('admin')->id(),
                'reason'   => $request->reason,
            ]);

            if (core()->getConfigData('sales.wallet.events.publish_balance_updated')) {
                Event::dispatch(new WalletBalanceUpdated(
                    customerId: $customer->id,
                    oldBalance: $oldBalance,
                    newBalance: $customer->fresh()->balanceFloatNum,
                    reason: 'admin_grant',
                    customerGroupId: $customer->customer_group_id,
                ));
            }

            return redirect()
                ->route('admin.customers.wallet.index', $id)
                ->with('success', trans('bagisto-wallet::app.admin.customers.wallet.adjust-add-success'));
        }

        if (! $customer->canWithdrawFloat($request->amount)) {
            return back()->withErrors([
                'amount' => trans('bagisto-wallet::app.admin.customers.wallet.insufficient-balance'),
            ]);
        }

        $oldBalance = $customer->balanceFloatNum;

        $customer->withdrawFloat($request->amount, [
            'type'     => 'admin_deduct',
            'admin_id' => auth()->guard('admin')->id(),
            'reason'   => $request->reason,
        ]);

        if (core()->getConfigData('sales.wallet.events.publish_balance_updated')) {
            Event::dispatch(new WalletBalanceUpdated(
                customerId: $customer->id,
                oldBalance: $oldBalance,
                newBalance: $customer->fresh()->balanceFloatNum,
                reason: 'admin_deduct',
            ));
        }

        return redirect()
            ->route('admin.customers.wallet.index', $id)
            ->with('success', trans('bagisto-wallet::app.admin.customers.wallet.adjust-deduct-success'));
    }
}
