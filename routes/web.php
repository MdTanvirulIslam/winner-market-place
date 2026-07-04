<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Store\AccountController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\DownloadController;
use App\Http\Controllers\Store\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'home'])->name('home');
Route::get('/products', [StoreController::class, 'index'])->name('store.products');
Route::get('/products/{product:slug}', [StoreController::class, 'show'])->name('store.products.show');

Route::middleware('auth')->group(function () {
    Route::get('/checkout/{product:slug}', [CheckoutController::class, 'show'])->name('store.checkout');
    Route::post('/checkout/{product:slug}', [CheckoutController::class, 'store'])->name('store.checkout.store');

    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{order}', [AccountController::class, 'order'])->name('account.orders.show');
    Route::get('/account/downloads', [AccountController::class, 'downloads'])->name('account.downloads');
    Route::get('/account/downloads/{order}/{release}', [DownloadController::class, 'download'])
        ->middleware('signed')
        ->name('account.download');
});

// Breeze and framework code link to the generic "dashboard" route; send each
// role where it belongs instead of keeping a separate page here.
Route::get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('home');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/admin.php';
require __DIR__.'/auth.php';
