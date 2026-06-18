<?php

namespace Webkul\MobilePass\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Webkul\MobilePass\Services\MobilePassService;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class PassController extends Controller
{
    public function __construct(private MobilePassService $service) {}

    public function lookup(int $id): JsonResponse
    {
        $customer = WalletCustomer::find($id);

        if (! $customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $customer->id,
                'name' => $customer->first_name.' '.$customer->last_name,
                'balance' => core()->formatPrice($customer->balanceFloatNum),
            ],
        ]);
    }

    public function destroy(int $customerId): RedirectResponse
    {
        $this->service->deleteGooglePass($customerId);

        session()->flash('success', trans('mobile-pass::app.admin.customers.pass-status-card.delete-success'));

        return redirect()->back();
    }
}
