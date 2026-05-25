<?php

use Illuminate\Support\Facades\Route;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\RentalPricing\Http\Controllers\Admin\DayPricingController;

Route::middleware('web')->group(function () {
    Route::group([
        'middleware' => ['admin', NoCacheMiddleware::class],
        'prefix'     => config('app.admin_url'),
    ], function () {
        Route::prefix('catalog/products')->group(function () {
            Route::controller(DayPricingController::class)
                ->prefix('{bookingProductId}/day-pricings')
                ->group(function () {
                    Route::get('', 'index')->name('admin.rental-pricing.day-pricings.index');
                    Route::post('', 'store')->name('admin.rental-pricing.day-pricings.store');
                });
        });
    });
});
