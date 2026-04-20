<?php

namespace Webkul\CustomerVerification\Listeners;

use Illuminate\Support\Facades\Auth;
use Webkul\CustomerVerification\Support\Verification;
use Webkul\Product\Repositories\ProductRepository;

class PreventUnverifiedRentalAddToCartListener
{
    public function __construct(private ProductRepository $productRepository) {}

    public function handle($productOrId): void
    {
        if (! Auth::guard('customer')->check()) {
            return;
        }

        // Real event (checkout.cart.add.before) passes product ID as int.
        // Test path may pass a product object directly.
        $product = is_int($productOrId) || (is_string($productOrId) && is_numeric($productOrId))
            ? $this->productRepository->find($productOrId)
            : $productOrId;

        if (! $this->isRentalProduct($product)) {
            return;
        }

        $customer = Auth::guard('customer')->user();

        if ($customer->verification_status !== Verification::STATUS_APPROVED) {
            throw new \Exception(
                trans('customer-verification::app.common.cannot_add_rental_unverified', [
                    'dashboard_url' => route('shop.customer.verification.index'),
                ])
            );
        }
    }

    private function isRentalProduct($product): bool
    {
        if (! $product) {
            return false;
        }

        if ($product->type === 'rental') {
            return true;
        }

        if (method_exists($product, 'getAttribute')) {
            return (bool) $product->getAttribute('requires_verification');
        }

        return false;
    }
}
