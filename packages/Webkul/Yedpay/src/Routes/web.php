<?php

use Illuminate\Support\Facades\Route;
use Webkul\Yedpay\Http\Controllers\YedpayController;

Route::controller(YedpayController::class)
    ->middleware('web')
    ->prefix('yedpay')
    ->group(function () {
        Route::get('redirect', 'redirect')->name('yedpay.standard.redirect');
        Route::get('success', 'success')->name('yedpay.payment.success');
        Route::get('cancel', 'cancel')->name('yedpay.payment.cancel');
        Route::post('notify', 'notify')->name('yedpay.payment.notify');
    });
