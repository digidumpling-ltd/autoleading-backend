<?php

use Illuminate\Support\Facades\Route;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\Wallet\Http\Controllers\Admin\WalletController;

Route::middleware('web')->group(function () {
    Route::group([
        'middleware' => ['admin', NoCacheMiddleware::class],
        'prefix'    => config('app.admin_url'),
    ], function () {
        Route::prefix('customers/{id}/wallet')->group(function () {
            Route::get('', [WalletController::class, 'index'])->name('admin.customers.wallet.index');
            Route::post('adjust', [WalletController::class, 'adjust'])->name('admin.customers.wallet.adjust');
        });
    });
});
