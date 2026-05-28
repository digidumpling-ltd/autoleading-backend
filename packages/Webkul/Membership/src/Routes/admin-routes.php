<?php

use Illuminate\Support\Facades\Route;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\Membership\Http\Controllers\Admin\TierRuleController;

Route::middleware('web')->group(function () {
    Route::group([
        'middleware' => ['admin', NoCacheMiddleware::class],
        'prefix'     => config('app.admin_url'),
    ], function () {
        Route::controller(TierRuleController::class)
            ->prefix('membership/tiers')
            ->group(function () {
                Route::get('', 'index')->name('admin.membership.tiers.index');
                Route::post('', 'store')->name('admin.membership.tiers.store');
            });

    });
});
