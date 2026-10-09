<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrDownloadController;
use App\Livewire\Dashboard\QrBuilder;
use App\Livewire\Dashboard\QrIndex;
use App\Livewire\Dashboard\QrShow;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::post('/locale/{locale}', LocaleController::class)->name('locale.update');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::livewire('/qr', QrIndex::class)->name('qr.index');
    Route::livewire('/qr/create', QrBuilder::class)->name('qr.create');
    Route::livewire('/qr/{qrCode}', QrShow::class)->name('qr.show');
    Route::livewire('/qr/{qrCode}/edit', QrBuilder::class)->name('qr.edit');
    Route::get('/qr/{qrCode}/download', QrDownloadController::class)->name('qr.download');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

if (app()->environment('local', 'testing')) {
    Route::view('/dev/ui', 'dev.ui')->name('dev.ui');
}
