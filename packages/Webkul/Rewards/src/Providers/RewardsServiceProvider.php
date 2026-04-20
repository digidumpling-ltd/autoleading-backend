<?php

namespace Webkul\Rewards\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Route;
use Webkul\Rewards\Http\Controllers\Shop\OnepageController;
use Webkul\Rewards\Http\Controllers\Shop\ReviewController;
use Webkul\Sales\Repositories\OrderRepository as BaseOrderRepository;
use Webkul\Rewards\Repositories\OrderRepository;
use Webkul\Rewards\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\InvoiceRepository as BaseInvoiceRepository;
use Webkul\Shop\Http\Controllers\API\OnepageController as APIOnepageController;
use Webkul\Shop\Http\Controllers\API\ReviewController as APIReviewController;

class RewardsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /* loaders */
        Route::middleware('web')->group(__DIR__ . '/../Routes/admin-routes.php');
        
        Route::middleware('web')->group(__DIR__ . '/../Routes/front-routes.php');

        $this->app->register(ModuleServiceProvider::class);

        $this->app->register(EventServiceProvider::class);

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'rewards');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'rewards');

        $this->publishable();

        $loader = AliasLoader::getInstance();

        $loader->alias('cart', CartFacade::class);

        $this->app->singleton('cart', function () {
            return new Cart();
        });

        if (core()->getConfigData('reward.general.general.module-status')) {
            $this->mergeConfigFrom(
                dirname(__DIR__) . '/Config/admin-menu.php',
                'menu.admin'
            );

            $this->mergeConfigFrom(
                dirname(__DIR__) . '/Config/shop-menu.php',
                'menu.customer'
            );
        }

        $this->app->bind('cart', 'Webkul\Rewards\Cart');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRewardModelBindings();

        $this->registerConfig();

        $this->registerCommands();

        /**
         * Bind Sales OrderRepository to Reward OrderRepository
         */
        $this->app->bind(BaseOrderRepository::class, OrderRepository::class);

        $this->app->bind(BaseInvoiceRepository::class, InvoiceRepository::class);

        $this->app->bind(APIOnepageController::class, OnepageController::class);

        $this->app->bind(APIReviewController::class, ReviewController::class);
    }

    /**
     * Register rewards model bindings early so repositories can resolve during package discovery.
     */
    protected function registerRewardModelBindings(): void
    {
        $this->app->concord->registerModel(
            \Webkul\Rewards\Contracts\ProductReward::class,
            \Webkul\Rewards\Models\ProductReward::class
        );

        $this->app->concord->registerModel(
            \Webkul\Rewards\Contracts\ProductSpecificTimeReward::class,
            \Webkul\Rewards\Models\ProductSpecificTimeReward::class
        );

        $this->app->concord->registerModel(
            \Webkul\Rewards\Contracts\CategoryReward::class,
            \Webkul\Rewards\Models\CategoryReward::class
        );

        $this->app->concord->registerModel(
            \Webkul\Rewards\Contracts\CategorySpecificTimeReward::class,
            \Webkul\Rewards\Models\CategorySpecificTimeReward::class
        );

        $this->app->concord->registerModel(
            \Webkul\Rewards\Contracts\CartReward::class,
            \Webkul\Rewards\Models\CartReward::class
        );

        $this->app->concord->registerModel(
            \Webkul\Rewards\Contracts\AttributeReward::class,
            \Webkul\Rewards\Models\AttributeReward::class
        );

        $this->app->concord->registerModel(
            \Webkul\Rewards\Contracts\RewardPoint::class,
            \Webkul\Rewards\Models\RewardPoint::class
        );

        $this->app->concord->registerModel(
            \Webkul\Rewards\Contracts\RedemptionSetting::class,
            \Webkul\Rewards\Models\RedemptionSetting::class
        );
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php',
            'core'
        );
    }

    /**
     * Publish assets.
     *
     * @return void
     */
    protected function publishable()
    {
        $this->publishes([
            __DIR__ . '/../Resources/views/shop/products/view/reviews.blade.php' 
            => resource_path('themes/default/views/products/view/reviews.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/customers/account/orders/view.blade.php' 
            => resource_path('themes/default/views/customers/account/orders/view.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/admin/sales/orders/view.blade.php' 
            => resource_path('views/vendor/admin/sales/orders/view.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/admin/sales/invoices/view.blade.php' 
            => resource_path('views/vendor/admin/sales/invoices/view.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/admin/sales/invoices/pdf.blade.php' 
            => resource_path('views/vendor/admin/sales/invoices/pdf.blade.php'),
        ]);

        /**
         * Over ride all existing core models of Bagisto
         */
        $this->overrideModels();

        /**
         * Publish Assets
         */
        $this->publishAssets();
    }

    /**
     * Register the console commands of this package
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Webkul\Rewards\Console\Commands\RewardByDateOfBirth::class,
                \Webkul\Rewards\Console\Commands\CheckRewardExpire::class,
                \Webkul\Rewards\Console\Commands\DisableTimeReward::class,
            ]);
        }
    }

    /**
     * Override the existing models
     */
    public function overrideModels()
    {
        $this->app->concord->registerModel(\Webkul\Customer\Contracts\Customer::class, \Webkul\Rewards\Models\Customer::class);

        $this->app->concord->registerModel(\Webkul\Checkout\Contracts\Cart::class, \Webkul\Rewards\Models\Cart::class);
    }

    /**
     * Publish the assets.
     *
     * @return void
     */
    protected function publishAssets()
    {
        $this->publishes([
            __DIR__ .'/../../publishable' => public_path('themes/reward')
        ], 'public');
    }
}