<?php

use Illuminate\Support\Facades\Route;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\CustomForm\Http\Controllers\Admin\TvdSubmissionController;

Route::middleware('web')->group(function () {
    Route::group([
        'middleware' => ['admin', NoCacheMiddleware::class],
        'prefix' => config('app.admin_url'),
    ], function () {
        Route::controller(TvdSubmissionController::class)
            ->prefix('tvd-submissions')
            ->group(function () {
                Route::get('', 'index')->name('admin.tvd-form.index');
                Route::get('{id}', 'show')->name('admin.tvd-form.show');
            });
    });
});
