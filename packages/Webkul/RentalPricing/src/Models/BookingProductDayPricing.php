<?php

namespace Webkul\RentalPricing\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\RentalPricing\Contracts\BookingProductDayPricing as BookingProductDayPricingContract;

class BookingProductDayPricing extends Model implements BookingProductDayPricingContract
{
    protected $fillable = [
        'booking_product_id',
        'min_days',
        'max_days',
        'discount_value',
        'discount_type',
    ];

    protected $casts = [
        'booking_product_id' => 'integer',
        'min_days'           => 'integer',
        'max_days'           => 'integer',
        'discount_value'     => 'float',
    ];
}
