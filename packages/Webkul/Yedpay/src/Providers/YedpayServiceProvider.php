<?php

namespace Webkul\Yedpay\Providers;

use Illuminate\Support\ServiceProvider;

class YedpayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/payment-methods.php',
            'payment_methods'
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'yedpay');

        $this->app->bind(\Webkul\Yedpay\Services\YedpayService::class, function ($app) {
            $yedpay = $app->make(\Webkul\Yedpay\Payment\Yedpay::class);

            return new \Webkul\Yedpay\Services\YedpayService(
                apiKey: $yedpay->getApiKey() ?? '',
                signingKey: $yedpay->getSigningKey() ?? '',
                sandbox: $yedpay->isSandbox(),
            );
        });
    }
}
