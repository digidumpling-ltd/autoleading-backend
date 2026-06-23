<?php

namespace Webkul\CustomPromotions\Http\Controllers\Shop;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\CustomPromotions\Repositories\RentalPromotionRuleRepository;
use Webkul\CustomPromotions\Services\ConditionEvaluator;
use Webkul\Product\Repositories\ProductRepository;

class RentalPromoCheckController extends Controller
{
    public function __construct(
        protected RentalPromotionRuleRepository $ruleRepository,
        protected ConditionEvaluator $conditionEvaluator,
        protected ProductRepository $productRepository,
    ) {}

    public function check(Request $request): JsonResponse
    {
        $customer = auth()->guard('customer')->user();

        if (! $customer) {
            return response()->json(['data' => []], 401);
        }

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $rentalTotalDays = 0;

        if ($dateFrom && $dateTo) {
            try {
                $rentalTotalDays = (int) round(
                    (Carbon::parse($dateTo)->timestamp - Carbon::parse($dateFrom)->timestamp) / 86400
                ) + 1;
            } catch (\Exception) {
                $rentalTotalDays = 0;
            }
        }

        $eventData = [
            'rental_total_days' => $rentalTotalDays,
            'rental_start_date' => $dateFrom,
            'rental_end_date' => $dateTo,
        ];

        $rules = $this->ruleRepository->getActiveRulesForBooking($customer);

        $matched = [];

        foreach ($rules as $rule) {
            $conditionsWithoutTotal = array_filter(
                $rule->conditions ?? [],
                fn ($c) => ($c['attribute'] ?? '') !== 'rental_total'
            );

            if ($this->conditionEvaluator->matches(
                array_values($conditionsWithoutTotal),
                (int) $rule->condition_type,
                $eventData,
                $customer
            )) {
                $matched[] = [
                    'id' => $rule->id,
                    'name' => $rule->name,
                    'actions' => [$this->describeAction($rule)],
                ];
            }
        }

        return response()->json(['data' => $matched]);
    }

    private function describeAction(mixed $rule): array
    {
        return match ($rule->action_type) {
            'free_extension' => [
                'type' => 'free_extension',
                'label' => trans('custom_promotions::app.shop.promo-card.free-extension-label', ['days' => (int) $rule->reward_value]),
            ],
            'reward_product' => $rule->reward_mode === 'note'
                ? ['type' => 'reward_product', 'label' => $rule->reward_value]
                : [
                    'type' => 'reward_product',
                    'label' => trans('custom_promotions::app.shop.promo-card.reward-product-label', [
                        'name' => optional($this->productRepository->find((int) $rule->reward_value))->name ?? 'Gift',
                    ]),
                ],
            'reward_points' => [
                'type' => 'reward_points',
                'label' => $rule->reward_mode === 'percentage'
                    ? trans('custom_promotions::app.shop.promo-card.reward-points-percent', ['percent' => $rule->reward_value])
                    : trans('custom_promotions::app.shop.promo-card.reward-points-fixed', ['points' => (int) $rule->reward_value]),
            ],
            'wallet_credit' => [
                'type' => 'wallet_credit',
                'label' => $rule->reward_mode === 'percentage'
                    ? trans('custom_promotions::app.shop.promo-card.wallet-credit-percent', ['percent' => $rule->reward_value])
                    : trans('custom_promotions::app.shop.promo-card.wallet-credit-fixed', ['amount' => $rule->reward_value]),
            ],
            default => ['type' => $rule->action_type, 'label' => $rule->action_type],
        };
    }
}
