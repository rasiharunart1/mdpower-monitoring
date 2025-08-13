<?php

use App\Http\Controllers\AlertsController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceSettingsController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

//google auth
Route::get('/', function () {
    return view('landing');
});
Route::get('/login', function () {
    return view('landing');
})->name('login');
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
Route::post('auth/google/logout', [GoogleController::class, 'logout'])->name('google.logout');
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
});
//protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/realtime', [DashboardController::class, 'getRealtimeData'])->name('dashboard.realtime');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/stream', [DashboardController::class, 'streamData'])->name('dashboard.stream');
    Route::get('/dashboard/check-changes', [DashboardController::class, 'getLastDataChange'])->name('dashboard.check-changes');


    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');
    Route::get('/logs/export', [LogsController::class, 'export'])->name('logs.export');
    Route::delete('/logs', [LogsController::class, 'destroy'])->name('logs.destroy');
    Route::delete('/logs/bulk', [LogsController::class, 'bulkDelete'])->name('logs.bulk-delete');

    Route::get('/device-settings', [DeviceSettingsController::class, 'index'])->name('device-settings.index');
    Route::post('/device-settings/update', [DeviceSettingsController::class, 'update'])->name('device-settings.update');
    Route::get('/device-settings/{deviceCode}', [DeviceSettingsController::class, 'show'])->name('device-settings.show');


    });
