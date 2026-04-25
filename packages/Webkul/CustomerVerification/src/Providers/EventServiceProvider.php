<?php

namespace Webkul\CustomerVerification\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Webkul\CustomerVerification\Listeners\HandleCustomerRegistration;
use Webkul\CustomerVerification\Listeners\PreventUnverifiedAddToCartListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        'customer.registration.after' => [
            HandleCustomerRegistration::class,
        ],
        'checkout.cart.add.before' => [
            PreventUnverifiedAddToCartListener::class,
        ],
        // NOTE: Login verification checks are deferred to Story 2.3 (admin approval workflow)
        // 'customer.after.login' => [
        //     HandleCustomerLogin::class,
        // ],
    ];
}