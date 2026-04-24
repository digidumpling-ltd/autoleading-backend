<?php

use Illuminate\Support\Facades\Route;
use Webkul\Wallet\Http\Controllers\Shop\Account\WalletController;
use Webkul\Wallet\Http\Controllers\Shop\Checkout\WalletCheckoutController;

Route::middleware(['web', 'theme', 'locale', 'currency', 'auth:customer'])
    ->prefix('customer/account/wallet')
    ->group(function () {
        Route::get('', [WalletController::class, 'index'])->name('shop.customers.account.wallet.index');
        Route::get('topup', [WalletController::class, 'topup'])->name('shop.customers.account.wallet.topup');
        Route::post('topup', [WalletController::class, 'processTopup'])->name('shop.customers.account.wallet.topup.store');
    });

Route::middleware(['web', 'theme', 'locale', 'currency', 'auth:customer'])
    ->prefix('shop/api/wallet')
    ->group(function () {
        Route::get('checkout-status', [WalletCheckoutController::class, 'status'])->name('shop.wallet.checkout.status');
    });
