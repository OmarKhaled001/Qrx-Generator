<?php

use App\Http\Controllers\Api\ApiSocialiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SocialiteController;

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


Route::get('/', [App\Http\Controllers\HomeController::class, 'getPlans'])->name('home');
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
Route::post('/', [App\Http\Controllers\ContactController::class, 'store'])->name('countact');
Route::get('/x/{code}', [App\Http\Controllers\QrxController::class, 'showQr'])->name('qr.show');
Route::get('checkout/{id?}', CheckoutController::class)->middleware('auth')->name('checkout');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('auth')->name('socialite.')->controller(SocialiteController::class)->group(function(){
    Route::get('/{provider}/callback','callback')->name('callback');
    Route::get('/{provider}/redirect','redirect')->name('redirect');
});

Route::prefix('api/auth')->name('socialite.')->controller(ApiSocialiteController::class)->group(function(){
    Route::get('/{provider}/callback','callback')->name('Apicallback');
    Route::get('/{provider}/redirect','redirect')->name('Apiredirect');
});


require __DIR__.'/auth.php';
