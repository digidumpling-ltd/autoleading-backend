<?php

namespace Webkul\CustomerVerification\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Webkul\CustomerVerification\Listeners\HandleCustomerRegistration;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        'customer.registration.after' => [
            HandleCustomerRegistration::class,
        ],
    ];
}