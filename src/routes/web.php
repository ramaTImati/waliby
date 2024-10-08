<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Waliby Web Routes
|--------------------------------------------------------------------------
|
*/

Route::prefix('waliby')->name('waliby.')->middleware('web')->group(function(){
    Route('/', function(){
        return view('templates');
    });
});