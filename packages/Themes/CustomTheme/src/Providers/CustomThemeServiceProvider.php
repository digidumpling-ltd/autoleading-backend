<?php

namespace Themes\CustomTheme\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Themes\CustomTheme\Listeners\Order;

class CustomThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'custom-theme');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'custom-theme');

        $this->mergeConfigFrom(__DIR__.'/../Config/system.php', 'core');

        // Prepend CustomTheme views to the shop namespace so queue-context emails
        // (no active HTTP request / ThemeViewFinder) resolve our overrides first.
        $this->callAfterResolving('view', function ($view) {
            $view->prependNamespace('shop', __DIR__.'/../Resources/views');
        });

        Event::listen('sales.order.update-status.after', [Order::class, 'afterOrderConfirmed']);
    }
}
