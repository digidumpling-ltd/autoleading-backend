<?php

namespace Themes\CustomTheme\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Themes\CustomTheme\Helpers\Indexers\Price\Booking as BookingIndexer;
use Themes\CustomTheme\Listeners\Order;
use Themes\CustomTheme\Type\Booking as CustomBookingType;
use Webkul\Product\Helpers\Indexers\Price\Booking as BaseBookingIndexer;

class CustomThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/system.php', 'core');

        $this->app->bind(BaseBookingIndexer::class, BookingIndexer::class);

        config(['product_types.booking.class' => CustomBookingType::class]);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'custom-theme');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'custom-theme');

        Blade::anonymousComponentPath(__DIR__.'/../Resources/views/components', 'shop');

        // Prepend CustomTheme views to the shop namespace so queue-context emails
        // (no active HTTP request / ThemeViewFinder) resolve our overrides first.
        $this->callAfterResolving('view', function ($view) {
            $view->prependNamespace('shop', __DIR__.'/../Resources/views');
        });

        Event::listen('sales.order.update-status.after', [Order::class, 'afterOrderConfirmed']);
    }
}
