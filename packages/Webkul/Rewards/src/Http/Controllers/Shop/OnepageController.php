<?php

namespace Webkul\Rewards\Http\Controllers\Shop;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\CartRule\Exceptions\CouponUsageLimitExceededException;
use Webkul\Checkout\Facades\Cart;
use Webkul\Payment\Facades\Payment;
use Webkul\Rewards\Helpers\CartHelper;
use Webkul\Rewards\Http\Resources\CartResource;
use Webkul\Rewards\Repositories\RewardPointRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\Shop\Http\Controllers\API\OnepageController as BaseController;

class OnepageController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CartHelper $cartHelper,
        protected OrderRepository $orderRepository,
        protected RewardPointRepository $rewardPointRepository,
    ) {
    }

    /**
     * Return cart summary — uses Rewards CartResource so `points` field is included.
     */
    public function summary(): JsonResource
    {
        $cart = Cart::getCart();

        return new CartResource($cart);
    }

    /**
     * Store order
     */
    public function storeOrder(): JsonResource
    {
        if (Cart::hasError()) {
            return new JsonResource([
                'redirect'     => true,
                'redirect_url' => route('shop.checkout.cart.index'),
            ]);
        }

        Cart::collectTotals();

        try {
            $this->validateOrder();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        $cart = Cart::getCart();

        if ($redirectUrl = Payment::getRedirectUrl($cart)) {
            return new JsonResource([
                'redirect'     => true,
                'redirect_url' => $redirectUrl,
            ]);
        }

        $data = array_merge((new OrderResource($cart))->jsonSerialize(), [
            'points'        => $cart->points,
            'points_amount' => $cart->points ? $this->cartHelper->redemption($cart->points) : null,
        ]);

        try {
            $order = $this->orderRepository->create($data);
        } catch (CouponUsageLimitExceededException $e) {
            cart()->removeCouponCode();

            Cart::collectTotals();

            return new JsonResource([
                'redirect' => false,
                'message'  => trans('shop::app.checkout.coupon.usage-limit-exceeded'),
            ]);
        }

        if (core()->getConfigData('reward.general.general.module-status')) {
            $this->rewardPointRepository->create($order);
        }

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return new JsonResource([
            'redirect'     => true,
            'redirect_url' => route('shop.checkout.onepage.success'),
        ]);
    }

    /**
     * Validate order before creation.
     *
     * @return void|\Exception
     */
    public function validateOrder()
    {
        $cart = Cart::getCart();

        $minimumOrderAmount = core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0;

        if (
            auth()->guard('customer')->check()
            && auth()->guard('customer')->user()->is_suspended
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.suspended-account-message'));
        }

        if (
            auth()->guard('customer')->user()
            && ! auth()->guard('customer')->user()->status
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.inactive-account-message'));
        }

        if (! $cart->checkMinimumOrder()) {
            throw new \Exception(trans('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]));
        }

        if ($cart->haveStockableItems() 
            && ! $cart->shipping_address
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.check-shipping-address'));
        }

        if (! $cart->billing_address) {
            throw new \Exception(trans('shop::app.checkout.cart.check-billing-address'));
        }

        if (
            $cart->haveStockableItems()
            && ! $cart->selected_shipping_rate
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-shipping-method'));
        }

        if (! $cart->payment) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-payment-method'));
        }
    }
}