<?php

use Illuminate\Support\Facades\Route;
use Webkul\CustomerVerification\Http\Controllers\Admin\VerificationManagementController;

Route::prefix('admin')
    ->middleware(['admin'])
    ->group(function (): void {
        Route::controller(VerificationManagementController::class)->group(function (): void {
            Route::get('verification', 'index')->name('admin.verification.index');
            Route::get('verification/{customerId}', 'show')->name('admin.verification.show');
            Route::post('verification/{customerId}/approve', 'approve')->name('admin.verification.approve');
            Route::post('verification/{customerId}/reject', 'reject')->name('admin.verification.reject');
            Route::post('verification/{customerId}/document/{docType}', 'uploadDocument')->name('admin.verification.document.upload');
            Route::delete('verification/{customerId}/document/{documentId}', 'destroyDocument')->name('admin.verification.document.destroy');
        });
    });
