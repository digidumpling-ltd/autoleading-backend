<?php

use Illuminate\Support\Facades\Route;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\CustomPromotions\Http\Controllers\Admin\RentalPromotionRuleController;
use Webkul\CustomPromotions\Http\Controllers\Admin\WalletPromotionRuleController;

Route::middleware('web')->group(function () {
    Route::group([
        'middleware' => ['admin', NoCacheMiddleware::class],
        'prefix' => config('app.admin_url'),
    ], function () {
        Route::controller(WalletPromotionRuleController::class)
            ->prefix('marketing/promotions/wallet-rules')
            ->group(function () {
                Route::get('', 'index')->name('admin.custom_promotions.wallet_rules.index');
                Route::get('create', 'create')->name('admin.custom_promotions.wallet_rules.create');
                Route::post('create', 'store')->name('admin.custom_promotions.wallet_rules.store');
                Route::get('edit/{id}', 'edit')->name('admin.custom_promotions.wallet_rules.edit');
                Route::put('edit/{id}', 'update')->name('admin.custom_promotions.wallet_rules.update');
                Route::delete('edit/{id}', 'destroy')->name('admin.custom_promotions.wallet_rules.destroy');
            });

        Route::controller(RentalPromotionRuleController::class)
            ->prefix('marketing/promotions/rental-rules')
            ->group(function () {
                Route::get('', 'index')->name('admin.custom_promotions.rental_rules.index');
                Route::get('create', 'create')->name('admin.custom_promotions.rental_rules.create');
                Route::post('create', 'store')->name('admin.custom_promotions.rental_rules.store');
                Route::get('edit/{id}', 'edit')->name('admin.custom_promotions.rental_rules.edit');
                Route::put('edit/{id}', 'update')->name('admin.custom_promotions.rental_rules.update');
                Route::delete('edit/{id}', 'destroy')->name('admin.custom_promotions.rental_rules.destroy');
            });
    });
});
