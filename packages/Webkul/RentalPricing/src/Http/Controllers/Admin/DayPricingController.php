<?php

namespace Webkul\RentalPricing\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\BookingProduct\Repositories\BookingProductRepository;
use Webkul\RentalPricing\Repositories\BookingProductDayPricingRepository;

class DayPricingController extends Controller
{
    public function __construct(
        protected BookingProductRepository $bookingProductRepository,
        protected BookingProductDayPricingRepository $dayPricingRepository
    ) {}

    /**
     * Return existing day pricing rules for a booking product.
     */
    public function index(int $bookingProductId): JsonResponse
    {
        $rules = $this->dayPricingRepository->findWhere(['booking_product_id' => $bookingProductId]);

        return response()->json(['data' => $rules]);
    }

    /**
     * Validate and replace all day pricing rules for a booking product.
     */
    public function store(Request $request, int $bookingProductId): JsonResponse
    {
        $bookingProduct = $this->bookingProductRepository->find($bookingProductId);

        if (! $bookingProduct) {
            return response()->json(['message' => trans('rental-pricing::app.validation.booking-product-not-found')], 404);
        }

        $rules = $request->input('rules', []);

        if (! is_array($rules)) {
            $rules = [];
        }

        $errors = $this->validateRules($rules, $bookingProduct->rental_slot?->daily_price ?? 0);

        if (! empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $this->dayPricingRepository->syncRules($bookingProductId, $rules);

        return response()->json([
            'message' => trans('rental-pricing::app.admin.day-pricing.saved'),
            'data'    => $this->dayPricingRepository->findWhere(['booking_product_id' => $bookingProductId]),
        ]);
    }

    /**
     * Validate day pricing rules: non-overlapping, single open-ended, discount bounds.
     */
    private function validateRules(array $rules, float $dailyPrice): array
    {
        $errors = [];
        $openEndedCount = 0;
        $ranges = [];

        foreach ($rules as $i => $rule) {
            $minDays = (int) ($rule['min_days'] ?? 0);
            $maxDays = isset($rule['max_days']) && $rule['max_days'] !== '' && $rule['max_days'] !== null
                ? (int) $rule['max_days']
                : null;
            $discountType = $rule['discount_type'] ?? '';
            $discountValue = (float) ($rule['discount_value'] ?? 0);

            if ($minDays < 1) {
                $errors[] = trans('rental-pricing::app.validation.min-days-positive', ['index' => $i + 1]);
            }

            if ($maxDays !== null && $maxDays < $minDays) {
                $errors[] = trans('rental-pricing::app.validation.max-days-gte-min', ['index' => $i + 1]);
            }

            if (! in_array($discountType, ['fixed', 'percentage'])) {
                $errors[] = trans('rental-pricing::app.validation.invalid-discount-type', ['index' => $i + 1]);
            }

            if ($discountType === 'fixed' && $discountValue > $dailyPrice) {
                $errors[] = trans('rental-pricing::app.validation.fixed-exceeds-daily-price', ['index' => $i + 1]);
            }

            if ($discountType === 'percentage' && ($discountValue < 0 || $discountValue > 100)) {
                $errors[] = trans('rental-pricing::app.validation.percentage-out-of-range', ['index' => $i + 1]);
            }

            if ($maxDays === null) {
                $openEndedCount++;
            }

            if ($openEndedCount > 1) {
                $errors[] = trans('rental-pricing::app.validation.multiple-open-ended');
                break;
            }

            foreach ($ranges as $j => $existing) {
                $existingMax = $existing['max'] ?? PHP_INT_MAX;
                $currentMax = $maxDays ?? PHP_INT_MAX;

                if ($minDays <= $existingMax && $currentMax >= $existing['min']) {
                    $errors[] = trans('rental-pricing::app.validation.overlapping-ranges', [
                        'a' => $j + 1,
                        'b' => $i + 1,
                    ]);
                }
            }

            $ranges[] = ['min' => $minDays, 'max' => $maxDays ?? PHP_INT_MAX];
        }

        return $errors;
    }
}
