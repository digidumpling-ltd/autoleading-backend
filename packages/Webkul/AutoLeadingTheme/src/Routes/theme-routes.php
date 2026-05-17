<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'locale', 'currency'])->name('shop.')->group(function () {
    // FAQ route
    Route::get('faq', 'Webkul\AutoLeadingTheme\Http\Controllers\FaqController@index')
        ->name('faq.index');
});
