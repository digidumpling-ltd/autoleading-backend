<?php

use Illuminate\Support\Facades\Route;
use Webkul\MobilePass\Http\Controllers\Shop\PassController;

Route::middleware(['web', 'theme', 'locale', 'currency', 'customer'])
    ->group(function () {
        Route::get('customer/account/mobile-pass/google/save', [PassController::class, 'saveGoogle'])
            ->name('shop.customers.account.mobile-pass.google.save');

        Route::get('customer/account/mobile-pass/apple/save', [PassController::class, 'saveApple'])
            ->name('shop.customers.account.mobile-pass.apple.save');
    });

/*
 * Register the spatie/laravel-mobile-pass package routes (Apple .pkpass download
 * signed route + PassKit web-service endpoints for device registration/updates).
 * The package exposes these via a Route::mobilePass() macro that must be invoked
 * explicitly; without it, the Apple addToWalletUrl() points at an unregistered
 * route. Google does not need this (it uses pay.google.com save URLs).
 */
if (Route::hasMacro('mobilePass')) {
    Route::mobilePass();
}
