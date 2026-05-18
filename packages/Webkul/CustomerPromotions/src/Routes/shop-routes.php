<?php

use Illuminate\Support\Facades\Route;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\CustomerPromotions\Http\Controllers\Shop\PromotionController;
use Webkul\Shop\Http\Middleware\AuthenticateCustomer;

Route::middleware(['web', 'shop'])
    ->prefix('customer/account')
    ->group(function () {
        Route::middleware([AuthenticateCustomer::class, NoCacheMiddleware::class])
            ->controller(PromotionController::class)
            ->prefix('promotions')
            ->group(function () {
                Route::get('', 'index')->name('shop.customers.account.promotions.index');
            });
    });
