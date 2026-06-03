<?php

use Illuminate\Support\Facades\Route;
use Webkul\CustomForm\Http\Controllers\Shop\TvdFormController;

Route::middleware(['web', 'shop'])
    ->group(function () {
        Route::controller(TvdFormController::class)
            ->group(function () {
                Route::get('/tvd', 'index')->name('shop.tvd-form.index');
                Route::post('/tvd/submit', 'submit')->name('shop.tvd-form.submit');
            });
    });
