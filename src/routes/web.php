<?php

use Illuminate\Support\Facades\Route;
use Ramatimati\Waliby\App\Http\Controllers\MetaController;
use Ramatimati\Waliby\App\Http\Controllers\EventController;
use Ramatimati\Waliby\App\Http\Controllers\HistoryController;
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
        Route::get('show/{id}', [TemplateController::class, 'show'])->name('show');
        Route::put('update/{id}', [TemplateController::class, 'update'])->name('update');
    });

    Route::prefix('events')->name('events.')->group(function(){
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::get('show/{id}', [EventController::class, 'show'])->name('show');
        Route::get('receiver', [EventController::class, 'getReceiver'])->name('getReceiver');
        Route::get('message_template', [EventController::class, 'getMessageTemplate'])->name('getMessageTemplate');
        Route::post('sentManually/{id}', [EventController::class, 'sentManually'])->name('sentManually');
        Route::delete('destroy/{id}', [EventController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('history')->name('history.')->group(function(){
        Route::get('/', [HistoryController::class, 'index'])->name('index');
    });

    Route::prefix('meta')->name('metas.')->group(function(){
        Route::get('/', [MetaController::class, 'index'])->name('index');
        Route::post('update', [MetaController::class, 'update'])->name('update');
    });
});

Route::match(['GET', 'POST'], '/api/waliby/history/stats', [HistoryController::class, 'statsUpdate']);