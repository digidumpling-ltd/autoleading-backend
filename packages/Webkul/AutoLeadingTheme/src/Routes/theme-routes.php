<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'locale', 'currency'])->name('shop.')->group(function () {
    // Blog routes
    Route::get('blog', 'Webkul\AutoLeadingTheme\Http\Controllers\BlogController@index')
        ->name('blog.index');

    Route::get('blog/{slug}', 'Webkul\AutoLeadingTheme\Http\Controllers\BlogController@show')
        ->name('blog.show');

    // FAQ route
    Route::get('faq', 'Webkul\AutoLeadingTheme\Http\Controllers\FaqController@index')
        ->name('faq.index');
});
