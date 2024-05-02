<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/','Frontend.index')->name('home');

// Client Dashboard
Route::middleware('auth')->prefix('dashboard')->name('dashboard')->group(function () {
    // get qrs by status
    Route::get('/', [App\Http\Controllers\QrxController::class, 'statistics']);
    Route::get('/qr', [App\Http\Controllers\QrxController::class, 'getAllQr'])->name('.qr.all');
    Route::get('/qr-active', [App\Http\Controllers\QrxController::class, 'getAllActiveQr'])->name('.qr.active');
    Route::get('/qr-pause', [App\Http\Controllers\QrxController::class, 'getAllPauseQr'])->name('.qr.pause');
    Route::get('/qr-expire', [App\Http\Controllers\QrxController::class, 'getAllExpiredQr'])->name('.qr.expire');
    // change qrx status by id
    Route::put('/status/{id}', [App\Http\Controllers\QrxController::class, 'switchStatuQr'])->name('.qr.status');
    // delet qrx by id
    Route::delete('/delete/{id}', [App\Http\Controllers\QrxController::class, 'deleteQr'])->name('.qr.delete');
    // create new qrx by livewire
    Route::view('/create','Dashboard.qrx-create')->name('.qr.create');
    Route::view('/plan-subscripe','Dashboard.error-plan-subscripe')->name('.error.plan.subscripe');
    Route::view('/plan-upgrade','Dashboard.error-plan-upgrade')->name('.error.plan.upgrade');
    // edit qrx by livewire
    Route::get('/edit/{id}', [App\Http\Controllers\QrxController::class, 'editQr'])->name('.qr.edit');
    Route::get('/download', [App\Http\Controllers\QrxController::class, 'downloadQr'])->name('.qr.download');
});
Route::get('/x/{code}', [App\Http\Controllers\QrxController::class, 'showQr'])->name('qr.show');
Route::get('checkout/{id?}', CheckoutController::class)->middleware('auth')->name('checkout');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
