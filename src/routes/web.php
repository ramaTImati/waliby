<?php

use Illuminate\Support\Facades\Route;
use Ramatimati\Waliby\App\Http\Controllers\TemplateController;

/*
|--------------------------------------------------------------------------
| Waliby Web Routes
|--------------------------------------------------------------------------
|
*/

Route::prefix('waliby')->name('waliby.')->middleware('web')->group(function(){
    Route::prefix('templates')->name('templates.')->group(function(){
        Route::get('/', [TemplateController::class, 'index'])->name('index');
        Route::post('/', [TemplateController::class, 'store'])->name('store');
    });
});