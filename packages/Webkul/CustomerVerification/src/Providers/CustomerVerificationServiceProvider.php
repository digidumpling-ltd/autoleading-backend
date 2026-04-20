<?php

namespace Webkul\CustomerVerification\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\Core\Http\Middleware\PreventRequestsDuringMaintenance;
use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Observers\CustomerObserver;

class CustomerVerificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CustomerVerificationDocumentContract::class, CustomerVerificationDocument::class);

        $this->app->register(EventServiceProvider::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Register model observers
        Customer::observe(CustomerObserver::class);
    }
}
