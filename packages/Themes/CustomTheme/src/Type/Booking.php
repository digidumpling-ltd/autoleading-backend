<?php

namespace Themes\CustomTheme\Type;

use Webkul\Product\Type\Booking as BaseBooking;

class Booking extends BaseBooking
{
    /**
     * For rental products the rental rate is already stored in the price index
     * by the custom Booking indexer. Returning zero extras here prevents
     * getPriceHtml() from double-adding the rate on top of getMinimalPrice().
     *
     * Event products retain the original behaviour (base + cheapest ticket).
     */
    protected function getCheapestBookingExtras($bookingProduct): ?array
    {
        if ($bookingProduct->type === 'rental') {
            return ['regular' => 0, 'final' => 0];
        }

        return parent::getCheapestBookingExtras($bookingProduct);
    }
}
