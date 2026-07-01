<?php

namespace Themes\CustomTheme\Helpers\Indexers\Price;

use Webkul\Product\Helpers\Indexers\Price\Booking as BaseBooking;
use Webkul\Product\Type\Booking as BookingType;

class Booking extends BaseBooking
{
    /**
     * For rental booking products getPriceHtml() displays product->price + min
     * rental rate as the "starting from" price. We sync the price index to that
     * same value so the storefront price filter matches what the card shows.
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
     * Return the rental starting rate for the price index:
     *   daily        → daily_price
     *   hourly       → hourly_price
     *   daily_hourly → min(daily_price, hourly_price)
     *
     * getPriceHtml() reads getMinimalPrice() from this index and adds zero
     * extras (via the CustomTheme Booking type override), so the displayed
     * "starting from" price equals this rate exactly.
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
