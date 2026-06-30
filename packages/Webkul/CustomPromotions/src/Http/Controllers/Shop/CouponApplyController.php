<?php

namespace Webkul\CustomPromotions\Http\Controllers\Shop;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Webkul\CustomPromotions\Models\CustomPromotionCouponProxy;
use Webkul\CustomPromotions\Models\RentalPromotionRuleProxy;
use Webkul\CustomPromotions\Models\WalletPromotionRuleProxy;

class CouponApplyController extends Controller
{
    public function apply(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);

        $coupon = CustomPromotionCouponProxy::modelClass()::where('code', $request->code)->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => trans('custom_promotions::app.shop.coupon.coupon-not-found'),
            ], 422);
        }

        if ($coupon->usage_limit > 0 && $coupon->times_used >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => trans('custom_promotions::app.shop.coupon.coupon-usage-exceeded'),
            ], 422);
        }

        $customer = auth()->guard('customer')->user();

        if ($coupon->usage_per_customer > 0) {
            $customerUsage = DB::table('custom_promotion_coupon_usages')
                ->where('custom_promotion_coupon_id', $coupon->id)
                ->where('customer_id', $customer->id)
                ->value('times_used') ?? 0;

            if ($customerUsage >= $coupon->usage_per_customer) {
                return response()->json([
                    'success' => false,
                    'message' => trans('custom_promotions::app.shop.coupon.coupon-per-customer-exceeded'),
                ], 422);
            }
        }

        session(['custom_promo_coupon' => $request->code]);

        $ruleType = $coupon->promotion_type;

        $rule = $ruleType === 'wallet'
            ? WalletPromotionRuleProxy::modelClass()::find($coupon->promotion_id)
            : RentalPromotionRuleProxy::modelClass()::find($coupon->promotion_id);

        return response()->json([
            'success'   => true,
            'rule_name' => $rule?->name,
            'rule_type' => $ruleType,
        ]);
    }
}
