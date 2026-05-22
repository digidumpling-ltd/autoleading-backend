<?php

use Illuminate\Support\Facades\Route;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\OrderPriceOverride\Http\Controllers\Admin\OrderEditController;

Route::middleware('web')->group(function () {
    Route::group([
        'middleware' => ['admin', NoCacheMiddleware::class],
        'prefix'     => config('app.admin_url'),
    ], function () {
        Route::prefix('sales/orders')->group(function () {
            Route::post('{id}/price-override', [OrderEditController::class, 'store'])
                ->name('admin.sales.orders.price-override.store');
        });
    });
});
