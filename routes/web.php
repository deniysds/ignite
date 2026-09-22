<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\Modules\Website\Http\Controllers\WebsiteController::class, 'home'])->name('website.home');
Route::get('/locale/{lang}', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/notifications/count', function () {
        return response()->json([
            'count' => auth()->user()->unreadNotifications->count()
        ]);
    })->name('notifications.count');
});
