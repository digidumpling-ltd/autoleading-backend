<?php

namespace Webkul\RentalPricing\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\RentalPricing\Contracts\BookingProductDayPricing as BookingProductDayPricingContract;
use Webkul\RentalPricing\Models\BookingProductDayPricing;

class BookingProductDayPricingRepository extends Repository
{
    public function model(): string
    {
        return BookingProductDayPricingContract::class;
    }

    /**
     * Find the matching rule for a given booking product and rental day count.
     * Returns null when no rule matches (caller falls back to daily_price).
     */
    public function findMatchingRule(int $bookingProductId, int $days): ?BookingProductDayPricing
    {
        return $this->getModel()
            ->where('booking_product_id', $bookingProductId)
            ->where('min_days', '<=', $days)
            ->where(function ($q) use ($days) {
                $q->whereNull('max_days')->orWhere('max_days', '>=', $days);
            })
            ->first();
    }

    /**
     * Replace all day pricing rules for a booking product with the given array.
     */
    public function syncRules(int $bookingProductId, array $rules): void
    {
        $this->deleteWhere(['booking_product_id' => $bookingProductId]);

        foreach ($rules as $rule) {
            $this->create(array_merge($rule, ['booking_product_id' => $bookingProductId]));
        }
    }
}
