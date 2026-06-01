<?php

namespace Webkul\Rewards\Http\Controllers\Shop\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\CartRule\Repositories\CartRuleCouponRepository;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Repositories\CartItemRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Rewards\Http\Resources\CartResource;
use Webkul\Shop\Http\Controllers\API\CartController as BaseCartController;

class CartController extends BaseCartController
{
    public function __construct(
        ProductRepository $productRepository,
        CartRuleCouponRepository $cartRuleCouponRepository,
        protected CartItemRepository $cartItemRepository,
    ) {
        parent::__construct($productRepository, $cartRuleCouponRepository);
    }

    /**
     * Cart — returns Rewards CartResource so `points` field is included.
     */
    public function index(): JsonResource
    {
        if (! Cart::getCart()) {
            return new JsonResource(['data' => null]);
        }

        Cart::collectTotals();

        $response = [
            'data' => ($cart = Cart::getCart()) ? new CartResource($cart) : null,
        ];

        if (session()->has('info')) {
            $response['message'] = session()->get('info');
        }

        return new JsonResource($response);
    }

    /**
     * Override to handle child items (configurable products) that base removeItem() ignores.
     * Base Cart::removeItem() only checks top-level items (parent_id IS NULL), so passing
     * a child item ID silently returns false without deleting anything.
     */
    public function destroy(): JsonResource
    {
        $this->validate(request(), [
            'cart_item_id' => 'required|exists:cart_items,id',
        ]);

        $itemId = (int) request()->input('cart_item_id');

        $item = $this->cartItemRepository->find($itemId);

        $rootItemId = $item?->parent_id ?? $itemId;

        Cart::removeItem($rootItemId);

        Cart::collectTotals();

        return new JsonResource([
            'data'    => new CartResource(Cart::getCart()),
            'message' => trans('shop::app.checkout.cart.success-remove'),
        ]);
    }
}
