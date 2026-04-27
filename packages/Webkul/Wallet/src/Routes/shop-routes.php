<?php

use Illuminate\Support\Facades\Route;
use Webkul\Wallet\Http\Controllers\Shop\Account\WalletController;
use Webkul\Wallet\Http\Controllers\Shop\Checkout\WalletCheckoutController;
use Webkul\Wallet\Http\Controllers\Shop\WalletTopUpController;

Route::middleware(['web', 'theme', 'locale', 'currency', 'customer'])
    ->prefix('customer/account/wallet')
    ->group(function () {
        Route::get('', [WalletController::class, 'index'])->name('shop.customers.account.wallet.index');
        Route::get('topup', [WalletTopUpController::class, 'create'])->name('shop.customers.account.wallet.topup');
        Route::post('topup', [WalletTopUpController::class, 'store'])->name('shop.customers.account.wallet.topup.store');
    });

Route::middleware(['web', 'theme', 'locale', 'currency', 'customer'])
    ->prefix('shop/api/wallet')
    ->group(function () {
        Route::get('checkout-status', [WalletCheckoutController::class, 'status'])->name('shop.wallet.checkout.status');
    });
