<?php

use Illuminate\Support\Facades\Route;
use Webkul\Yedpay\Http\Controllers\YedpayController;
use Webkul\Yedpay\Http\Controllers\YedpayTopUpController;

Route::controller(YedpayController::class)
    ->middleware('web')
    ->prefix('yedpay')
    ->group(function () {
        Route::get('redirect', 'redirect')->name('yedpay.standard.redirect');
        Route::get('success', 'success')->name('yedpay.payment.success');
        Route::get('cancel', 'cancel')->name('yedpay.payment.cancel');
        Route::post('notify', 'notify')->name('yedpay.payment.notify');
    });

Route::controller(YedpayTopUpController::class)
    ->middleware(['web', 'customer'])
    ->prefix('yedpay/topup')
    ->group(function () {
        Route::get('redirect', 'redirect')->name('yedpay.topup.redirect');
        Route::get('success', 'success')->name('yedpay.topup.success');
        Route::get('cancel', 'cancel')->name('yedpay.topup.cancel');
    });

Route::controller(YedpayTopUpController::class)
    ->middleware('web')
    ->prefix('yedpay/topup')
    ->group(function () {
        Route::post('notify', 'notify')->name('yedpay.topup.notify');
    });
