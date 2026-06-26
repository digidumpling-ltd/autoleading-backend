<?php

namespace Themes\CustomTheme\Helpers\Indexers\Price;

use Webkul\Product\Helpers\Indexers\Price\Booking as BaseBooking;
use Webkul\Product\Type\Booking as BookingType;

class Booking extends BaseBooking
{
    /**
     * For rental booking products the base product price is irrelevant —
     * customers pay a per-day or per-hour rate, not a sale price. We replace
     * the price index with the rental starting rate so the storefront price
     * filter matches the "starting from" price displayed on the product card.
     *
     * Non-rental booking types fall through to the default indexer.
     */
    public function getIndices()
    {
        $startingPrice = $this->getRentalStartingPrice();

        if ($startingPrice === null) {
            return parent::getIndices();
        }

        return [
            'min_price'          => $startingPrice,
            'regular_min_price'  => $startingPrice,
            'max_price'          => $startingPrice,
            'regular_max_price'  => $startingPrice,
            'product_id'         => $this->product->id,
            'channel_id'         => $this->channel->id,
            'customer_group_id'  => $this->customerGroup->id,
        ];
    }

    /**
     * Return the rental starting price based on the configured renting_type:
     *   daily        → daily_price
     *   hourly       → hourly_price
     *   daily_hourly → min(daily_price, hourly_price)
     *
     * Returns null for non-rental booking types.
     */
    protected function getRentalStartingPrice(): ?float
    {
        /** @var BookingType $typeInstance */
        $typeInstance = $this->product->getTypeInstance()->setProduct($this->product);

        $bookingProduct = $typeInstance->getBookingProduct($this->product->id);

        if (! $bookingProduct || $bookingProduct->type !== 'rental') {
            return null;
        }

        $slot = $bookingProduct->rental_slot;

        if (! $slot) {
            return null;
        }

        $startingPrice = match ($slot->renting_type) {
            'daily'        => (float) $slot->daily_price,
            'hourly'       => (float) $slot->hourly_price,
            'daily_hourly' => min((float) $slot->daily_price, (float) $slot->hourly_price),
            default        => null,
        };

        return ($startingPrice > 0) ? $startingPrice : null;
    }
}
