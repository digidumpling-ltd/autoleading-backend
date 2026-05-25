<?php

namespace Webkul\RentalPricing\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Webkul\RentalPricing\Models\BookingProductDayPricing;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    protected $models = [
        BookingProductDayPricing::class,
    ];
}
