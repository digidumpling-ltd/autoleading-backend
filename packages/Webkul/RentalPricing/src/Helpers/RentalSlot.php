<?php

namespace Webkul\RentalPricing\Helpers;

use Carbon\Carbon;
use Webkul\BookingProduct\Helpers\RentalSlot as CoreRentalSlot;
use Webkul\Product\DataTypes\CartItemValidationResult;
use Webkul\RentalPricing\Repositories\BookingProductDayPricingRepository;

class RentalSlot extends CoreRentalSlot
{
    /**
     * Compute the effective per-day rate for a booking product given rental days.
     * Applies the matched discount rule off daily_price, or falls back to daily_price.
     */
    private function effectiveDailyRate(int $bookingProductId, float $dailyPrice, int $days): float
    {
        /** @var BookingProductDayPricingRepository $repo */
        $repo = app(BookingProductDayPricingRepository::class);

        $rule = $repo->findMatchingRule($bookingProductId, $days);

        if (! $rule) {
            return $dailyPrice;
        }

        if ($rule->discount_type === 'fixed') {
            return max(0, $dailyPrice - $rule->discount_value);
        }

        return $dailyPrice * (1 - $rule->discount_value / 100);
    }

    /**
     * {@inheritdoc}
     */
    public function addAdditionalPrices(array $products): array
    {
        $bookingProduct = $this->bookingProductRepository->findOneByField('product_id', $products[0]['product_id']);

        $rentingType = $products[0]['additional']['booking']['renting_type'] ?? $bookingProduct->rental_slot->renting_type;

        if ($rentingType == 'daily') {
            $from = Carbon::createFromTimeString($products[0]['additional']['booking']['date_from'].' 00:00:00');
            $to = Carbon::createFromTimeString($products[0]['additional']['booking']['date_to'].' 24:00:00');

            $days = (int) abs($to->diffInDays($from));
            $rate = $this->effectiveDailyRate($bookingProduct->id, (float) $bookingProduct->rental_slot->daily_price, $days);

            $basePrice = $rate * $days;
        } else {
            $from = Carbon::createFromTimestamp($products[0]['additional']['booking']['slot']['from']);
            $to = Carbon::createFromTimestamp($products[0]['additional']['booking']['slot']['to']);

            $basePrice = $bookingProduct->rental_slot->hourly_price * (int) abs($to->diffInHours($from));
        }

        $price = core()->convertPrice($basePrice);

        $quantity = $products[0]['quantity'];

        // Replace (not add) to avoid double-counting the daily_price already in the price index.
        $products[0]['price'] = $price;
        $products[0]['base_price'] = $basePrice;
        $products[0]['total'] = $price * $quantity;
        $products[0]['base_total'] = $basePrice * $quantity;

        return $products;
    }

    /**
     * {@inheritdoc}
     */
    public function validateCartItem($item): CartItemValidationResult
    {
        $result = new CartItemValidationResult;

        if (parent::isCartItemInactive($item)) {
            $result->itemIsInactive();

            return $result;
        }

        $bookingProduct = $this->bookingProductRepository->findOneByField('product_id', $item->product_id);

        $bookingInfo = $item->additional['booking'] ?? null;

        $rentingType = $bookingInfo['renting_type'] ?? $bookingProduct->rental_slot->renting_type;

        if ($rentingType == 'daily') {
            if (! isset($bookingInfo['date_from']) || ! isset($bookingInfo['date_to'])) {
                $result->itemIsInactive();

                return $result;
            }

            $from = Carbon::createFromTimeString($bookingInfo['date_from'].' 00:00:00');
            $to = Carbon::createFromTimeString($bookingInfo['date_to'].' 24:00:00');

            $days = (int) abs($to->diffInDays($from));
            $rate = $this->effectiveDailyRate($bookingProduct->id, (float) $bookingProduct->rental_slot->daily_price, $days);

            $price = $rate * $days;
        } else {
            if (
                ! isset($item->additional['booking']['slot']['from'])
                || ! isset($item->additional['booking']['slot']['to'])
            ) {
                $result->itemIsInactive();

                return $result;
            }

            $from = Carbon::createFromTimestamp($item->additional['booking']['slot']['from']);
            $to = Carbon::createFromTimestamp($item->additional['booking']['slot']['to']);

            $price = $bookingProduct->rental_slot->hourly_price * (int) abs($to->diffInHours($from));
        }

        if ($price == $item->base_price) {
            return $result;
        }

        $item->base_price = $price;
        $item->price = core()->convertPrice($price);

        $item->base_total = $price * $item->quantity;
        $item->total = core()->convertPrice($price * $item->quantity);

        $item->save();

        return $result;
    }
}
