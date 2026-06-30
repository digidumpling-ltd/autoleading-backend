<?php

namespace Webkul\MobilePass\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Webkul\MobilePass\Services\MobilePassService;

class PassController extends Controller
{
    public function __construct(protected MobilePassService $mobilePassService) {}

    public function saveGoogle()
    {
        if (! $this->mobilePassService->isEnabled() || ! $this->mobilePassService->hasGoogleCredentials()) {
            abort(404);
        }

        $customer = auth()->guard('customer')->user();

        $pass = $this->mobilePassService->createOrGetGooglePass($customer);

        return redirect($pass->addToWalletUrl());
    }

    public function saveApple()
    {
        if (! $this->mobilePassService->isEnabled() || ! $this->mobilePassService->hasAppleCredentials()) {
            abort(404);
        }

        $customer = auth()->guard('customer')->user();

        $pass = $this->mobilePassService->createOrGetApplePass($customer);

        return redirect($pass->addToWalletUrl());
    }
}
