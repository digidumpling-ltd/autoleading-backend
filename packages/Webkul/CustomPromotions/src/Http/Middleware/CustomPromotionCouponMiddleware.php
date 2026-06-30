<?php

namespace Webkul\CustomPromotions\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Webkul\Checkout\Facades\Cart;
use Webkul\CustomPromotions\Models\CustomPromotionCouponProxy;
use Webkul\Shop\Http\Resources\CartResource;

class CustomPromotionCouponMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->isMethod('DELETE') && $request->is('api/checkout/cart/coupon')) {
            session()->forget('custom_promo_coupon');

            return $next($request);
        }

        if (! ($request->isMethod('POST') && $request->is('api/checkout/cart/coupon'))) {
            return $next($request);
        }

        $code = $request->input('code');

        if (! $code) {
            return $next($request);
        }

        $coupon = CustomPromotionCouponProxy::modelClass()::where('code', $code)->first();

        if (! $coupon) {
            return $next($request);
        }

        if ($coupon->usage_limit > 0 && $coupon->times_used >= $coupon->usage_limit) {
            return (new JsonResource([
                'data'    => new CartResource(Cart::getCart()),
                'message' => trans('custom_promotions::app.shop.coupon.coupon-usage-exceeded'),
            ]))->response()->setStatusCode(422);
        }

        $customer = auth()->guard('customer')->user();

        if ($customer && $coupon->usage_per_customer > 0) {
            $customerUsage = DB::table('custom_promotion_coupon_usages')
                ->where('custom_promotion_coupon_id', $coupon->id)
                ->where('customer_id', $customer->id)
                ->value('times_used') ?? 0;

            if ($customerUsage >= $coupon->usage_per_customer) {
                return (new JsonResource([
                    'data'    => new CartResource(Cart::getCart()),
                    'message' => trans('custom_promotions::app.shop.coupon.coupon-per-customer-exceeded'),
                ]))->response()->setStatusCode(422);
            }
        }

        Cart::setCouponCode($code);

        session(['custom_promo_coupon' => $code]);

        return (new JsonResource([
            'data'    => new CartResource(Cart::getCart()),
            'message' => trans('shop::app.checkout.coupon.success-apply'),
        ]))->response();
    }
}
