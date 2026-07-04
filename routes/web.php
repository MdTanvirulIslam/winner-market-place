<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Store\AccountController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\DownloadController;
use App\Http\Controllers\Store\PageController;
use App\Http\Controllers\Store\PaymentController;
use App\Http\Controllers\Store\SitemapController;
use App\Http\Controllers\Store\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'home'])->name('home');
Route::get('/products', [StoreController::class, 'index'])->name('store.products');
Route::get('/products/{product:slug}', [StoreController::class, 'show'])->name('store.products.show');

Route::get('/about', [PageController::class, 'about'])->name('store.about');
Route::get('/terms', [PageController::class, 'terms'])->name('store.terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('store.privacy');
Route::get('/refund-policy', [PageController::class, 'refundPolicy'])->name('store.refund-policy');
Route::get('/contact', [PageController::class, 'contact'])->name('store.contact');
Route::post('/contact', [PageController::class, 'sendContact'])->middleware('throttle:5,10')->name('store.contact.send');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// SSLCommerz callbacks — cross-site POSTs, CSRF-exempt (see bootstrap/app.php),
// no auth (the browser POST arrives without session cookies). Validation is
// server-side; GET fallbacks cover gateways/users hitting them directly.
Route::middleware('throttle:60,1')->group(function () {
    Route::match(['get', 'post'], '/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::match(['get', 'post'], '/payment/fail', [PaymentController::class, 'fail'])->name('payment.fail');
    Route::match(['get', 'post'], '/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::post('/payment/ipn', [PaymentController::class, 'ipn'])->name('payment.ipn');
});

Route::middleware('auth')->group(function () {
    Route::get('/checkout/{product:slug}', [CheckoutController::class, 'show'])->name('store.checkout');
    Route::post('/checkout/{product:slug}', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('store.checkout.store');
    Route::post('/payment/{order}/start', [PaymentController::class, 'start'])->middleware('throttle:10,1')->name('payment.start');

    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{order}', [AccountController::class, 'order'])->name('account.orders.show');
    Route::get('/account/orders/{order}/invoice', [AccountController::class, 'invoice'])->name('account.orders.invoice');
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
