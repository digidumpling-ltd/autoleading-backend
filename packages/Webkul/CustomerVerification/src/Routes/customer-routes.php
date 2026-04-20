<?php

use Illuminate\Support\Facades\Route;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\CustomerVerification\Http\Controllers\Customer\VerificationDashboardController;

Route::prefix('customer')
    ->middleware(['customer', NoCacheMiddleware::class])
    ->group(function (): void {
        Route::controller(VerificationDashboardController::class)->group(function (): void {
            Route::get('verification', 'index')->name('shop.customer.verification.index');

            Route::post('verification/upload', 'upload')->name('shop.customer.verification.upload');
        });
    });
