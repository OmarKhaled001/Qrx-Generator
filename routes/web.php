<?php

use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::post('/locale/{locale}', LocaleController::class)->name('locale.update');

if (app()->environment('local', 'testing')) {
    Route::view('/dev/ui', 'dev.ui')->name('dev.ui');
}
