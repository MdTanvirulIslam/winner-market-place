<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Middleware\RequireAdmin;
use App\Http\Middleware\RequireSuperAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', RequireAdmin::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(RequireSuperAdmin::class)->group(function () {
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::resource('users', AdminUserController::class)->except(['show']);
    });
});
