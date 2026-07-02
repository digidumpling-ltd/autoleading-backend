<?php

namespace Webkul\Wallet\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Wallet\DataGrids\Admin\WalletTransactionDataGrid;
use Webkul\Wallet\Models\Customer as WalletCustomer;
use Webkul\Wallet\Services\WalletService;

class WalletController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

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
            $adminId = auth()->guard('admin')->id();

            $customer->depositFloat($validated['amount'], [
                'type'         => 'admin_grant',
                'admin_id'     => $adminId,
                'reason'       => $validated['reason'],
                'creator_type' => 'admin',
                'creator_id'   => $adminId,
            ]);

            $newBalance = $customer->fresh()->balanceFloatNum;

            if ($request->input('notify_customer')) {
                $this->walletService->notifyTopUp($customer, (float) $validated['amount'], $newBalance);
            }

            return response()->json([
                'message' => trans('bagisto-wallet::app.admin.customers.wallet.adjust-add-success'),
                'balance' => core()->formatPrice($newBalance),
            ]);
        }

        if (! $customer->canWithdrawFloat($validated['amount'])) {
            return response()->json([
                'errors' => [
                    'amount' => [trans('bagisto-wallet::app.admin.customers.wallet.insufficient-balance')],
                ],
            ], 422);
        }

        $adminId = auth()->guard('admin')->id();

        $customer->withdrawFloat($validated['amount'], [
            'type'         => 'admin_deduct',
            'admin_id'     => $adminId,
            'reason'       => $validated['reason'],
            'creator_type' => 'admin',
            'creator_id'   => $adminId,
        ]);

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
            $adminId = auth()->guard('admin')->id();

            $customer->depositFloat($request->amount, [
                'type'         => 'admin_grant',
                'admin_id'     => $adminId,
                'reason'       => $request->reason,
                'creator_type' => 'admin',
                'creator_id'   => $adminId,
            ]);

            $newBalance = $customer->fresh()->balanceFloatNum;

            if ($request->notify_customer) {
                $this->walletService->notifyTopUp($customer, (float) $request->amount, $newBalance);
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

        $adminId = auth()->guard('admin')->id();

        $customer->withdrawFloat($request->amount, [
            'type'         => 'admin_deduct',
            'admin_id'     => $adminId,
            'reason'       => $request->reason,
            'creator_type' => 'admin',
            'creator_id'   => $adminId,
        ]);

        return redirect()
            ->route('admin.customers.wallet.index', $id)
            ->with('success', trans('bagisto-wallet::app.admin.customers.wallet.adjust-deduct-success'));
    }
}
