<?php

namespace Webkul\OrderPriceOverride\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/acl.php',
            'acl'
        );
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(dirname(__DIR__).'/Resources/lang', 'order-price-override');

        $this->loadViewsFrom(dirname(__DIR__).'/Resources/views', 'order-price-override');

        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/admin-routes.php');

        Event::listen(
            'bagisto.admin.sales.order.page_action.after',
            function ($viewRenderEventManager) {
                $viewRenderEventManager->addTemplate('order-price-override::admin.sales.orders.edit-button');
            }
        );
    }
}
