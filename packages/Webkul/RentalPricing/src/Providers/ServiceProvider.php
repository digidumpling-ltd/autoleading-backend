<?php

namespace Webkul\RentalPricing\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Webkul\BookingProduct\Helpers\RentalSlot as CoreRentalSlot;
use Webkul\RentalPricing\Helpers\RentalSlot;
use Webkul\RentalPricing\Repositories\BookingProductDayPricingRepository;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CoreRentalSlot::class, RentalSlot::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__).'/Database/Migrations');

        $this->loadTranslationsFrom(dirname(__DIR__).'/Resources/lang', 'rental-pricing');

        $this->loadViewsFrom(dirname(__DIR__).'/Resources/views', 'rental-pricing');

        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/admin-routes.php');

        Event::listen(
            'bagisto.admin.catalog.product.edit.booking.rental.after',
            function ($viewRenderEventManager) {
                $viewRenderEventManager->addTemplate('rental-pricing::admin.day-pricing');
            }
        );

        // Expose day_pricing_rules in the shop rental view for any theme to consume.
        View::composer('shop::products.view.types.booking.rental', function ($view) {
            $bookingProduct = $view->getData()['bookingProduct'] ?? null;

            if (! $bookingProduct) {
                $view->with('day_pricing_rules', []);

                return;
            }

            $rentingType = $bookingProduct->rental_slot?->renting_type;

            if (! in_array($rentingType, ['daily', 'daily_hourly'])) {
                $view->with('day_pricing_rules', []);

                return;
            }

            $rules = app(BookingProductDayPricingRepository::class)
                ->findWhere(['booking_product_id' => $bookingProduct->id]);

            $view->with('day_pricing_rules', $rules);
        });
    }
}
