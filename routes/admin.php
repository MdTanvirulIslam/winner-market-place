<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReleaseController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Middleware\RequireAdmin;
use App\Http\Middleware\RequireSuperAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', RequireAdmin::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Catalog — manageable by staff and super admins alike.
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::resource('releases', ReleaseController::class)->except(['show']);

    Route::middleware(RequireSuperAdmin::class)->group(function () {
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::resource('users', AdminUserController::class)->except(['show']);
    });
});
