<?php

use Illuminate\Support\Facades\Route;
use Webkul\CustomPromotions\Http\Controllers\Shop\RentalPromoCheckController;

Route::middleware('web')->group(function () {
    Route::group(['middleware' => ['locale', 'theme', 'currency']], function () {
        Route::get('/api/custom-promotions/rental/check', [RentalPromoCheckController::class, 'check'])
            ->name('custom_promotions.rental.check');
    });
});
