<?php

namespace Webkul\CustomerVerification\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\Core\Http\Middleware\PreventRequestsDuringMaintenance;
use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Console\Commands\GenerateCustomerReferenceNumbers;
use Webkul\CustomerVerification\Contracts\CustomerVerificationDocument as CustomerVerificationDocumentContract;
use Webkul\CustomerVerification\Http\Middleware\VerificationCheckoutMiddleware;
use Webkul\CustomerVerification\Http\Middleware\VerificationTopUpMiddleware;
use Webkul\CustomerVerification\Models\CustomerVerificationDocument;
use Webkul\CustomerVerification\Observers\CustomerObserver;

class CustomerVerificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/menu.php',
            'menu.customer'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/admin-menu.php',
            'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/acl.php',
            'acl'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php',
            'core'
        );

        $this->app->bind(CustomerVerificationDocumentContract::class, CustomerVerificationDocument::class);

        $this->app->register(ModuleServiceProvider::class);

        $this->app->register(EventServiceProvider::class);
    }

    public function boot(): void
    {
        Route::middleware(['web', 'shop', PreventRequestsDuringMaintenance::class])
            ->group(__DIR__.'/../Routes/customer-routes.php');

        Route::middleware(['web'])
            ->group(__DIR__.'/../Routes/admin-routes.php');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'customer-verification');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'customer-verification');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->app['router']->pushMiddlewareToGroup('web', VerificationCheckoutMiddleware::class);

        // Push wallet top-up middleware to web group to gate top-ups when enabled
        $this->app['router']->pushMiddlewareToGroup('web', VerificationTopUpMiddleware::class);

        // Register artisan command for generating reference numbers
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCustomerReferenceNumbers::class,
            ]);
        }

        Customer::resolveRelationUsing('verificationDocuments', function ($customer) {
            return $customer->hasMany(CustomerVerificationDocument::class, 'customer_id');
        });

        Customer::observe(CustomerObserver::class);

        Event::listen('bagisto.shop.customers.account.profile.email.after', function () {
            $customer = auth()->guard('customer')->user();

            if (! $customer) {
                return;
            }

            return view('customer-verification::shop.customers.account.profile-status-row', [
                'status' => $customer->verification_status ?? 'incomplete',
            ])->render();
        });
    }
}
