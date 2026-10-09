<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrDownloadController;
use App\Http\Controllers\ScanController;
use App\Livewire\Dashboard\QrBuilder;
use App\Livewire\Dashboard\QrIndex;
use App\Livewire\Dashboard\QrShow;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// The public page every printed QR code opens. Keep this URL stable.
Route::middleware('throttle:scans')->group(function () {
    Route::get('/x/{code}', [ScanController::class, 'show'])->name('scan.show');
    Route::get('/x/{code}/contact.vcf', [ScanController::class, 'vcard'])->name('scan.vcard');
});

Route::post('/locale/{locale}', LocaleController::class)->name('locale.update');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::livewire('/qr', QrIndex::class)->name('qr.index');
    Route::livewire('/qr/create', QrBuilder::class)->name('qr.create');
    Route::livewire('/qr/{qrCode}', QrShow::class)->name('qr.show');
    Route::livewire('/qr/{qrCode}/edit', QrBuilder::class)->name('qr.edit');
    Route::get('/qr/{qrCode}/download', QrDownloadController::class)->name('qr.download');

    Route::get('/billing', [BillingController::class, 'show'])->name('billing');
    Route::get('/billing/checkout/{plan:slug}', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/billing/portal', [BillingController::class, 'portal'])->name('billing.portal');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

if (app()->environment('local', 'testing')) {
    Route::view('/dev/ui', 'dev.ui')->name('dev.ui');
}

// Unknown URLs go through the web middleware too, so the 404 page is localised.
Route::fallback(fn () => abort(404));
