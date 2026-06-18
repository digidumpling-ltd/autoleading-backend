<?php

use Illuminate\Support\Facades\Route;
use Webkul\MobilePass\Http\Controllers\Admin\PassController as AdminPassController;

Route::middleware(['web', 'admin'])
    ->prefix(config('app.admin_url').'/api')
    ->group(function () {
        Route::get('customers/mobile-pass/lookup/{id}', [AdminPassController::class, 'lookup'])
            ->name('admin.api.customers.mobile-pass.lookup');
    });

Route::middleware(['web', 'admin'])
    ->prefix(config('app.admin_url'))
    ->group(function () {
        Route::delete('customers/{customerId}/mobile-pass', [AdminPassController::class, 'destroy'])
            ->name('admin.customers.mobile-pass.destroy');
    });
