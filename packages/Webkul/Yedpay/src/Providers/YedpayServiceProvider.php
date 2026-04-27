<?php

namespace Webkul\Yedpay\Providers;

use Illuminate\Support\ServiceProvider;
use Webkul\Yedpay\Payment\Yedpay;
use Webkul\Yedpay\Services\YedpayService;

class YedpayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/payment-methods.php',
            'payment_methods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php',
            'core'
        );

        $this->app->bind(YedpayService::class, function ($app) {
            $yedpay = $app->make(Yedpay::class);

            return new YedpayService(
                apiKey: $yedpay->getApiKey() ?? '',
                signingKey: $yedpay->getSigningKey() ?? '',
                sandbox: $yedpay->isSandbox(),
            );
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'yedpay');
    }
}
