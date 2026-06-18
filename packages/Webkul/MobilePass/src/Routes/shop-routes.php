<?php

use Illuminate\Support\Facades\Route;
use Webkul\MobilePass\Http\Controllers\Shop\PassController;

Route::middleware(['web', 'theme', 'locale', 'currency', 'customer'])
    ->group(function () {
        Route::get('customer/account/mobile-pass/google/save', [PassController::class, 'saveGoogle'])
            ->name('shop.customers.account.mobile-pass.google.save');
    });
