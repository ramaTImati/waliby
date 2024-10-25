<?php

use Illuminate\Support\Facades\Route;
use Ramatimati\Waliby\App\Http\Controllers\HistoryController;
use Ramatimati\Waliby\App\Http\Controllers\EventController;
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

    Route::prefix('event')->name('event.')->group(function(){
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::get('/show/{id}', [EventController::class, 'show'])->name('show');
        Route::get('/receiver', [EventController::class, 'getReceiver'])->name('getReceiver');
        Route::get('/message_template', [EventController::class, 'getMessageTemplate'])->name('getMessageTemplate');
        Route::post('/sent', [EventController::class, 'sentEvent'])->name('sentEvent');
    });

    Route::prefix('history')->name('history.')->group(function(){
        Route::get('/', [HistoryController::class, 'index'])->name('index');
        Route::post('stats', [HistoryController::class, 'statsUpdate']);
    });
});