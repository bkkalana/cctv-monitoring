<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Cameras
    Route::resource('cameras', CameraController::class);

    // Faces
    Route::resource('faces', FaceController::class);

    // Alerts
    Route::resource('alerts', AlertController::class)->except(['create', 'store', 'edit', 'update']);

    // Videos
    Route::resource('videos', VideoController::class)->except(['create', 'store', 'edit', 'update']);
    Route::get('/videos/{video}/download', [VideoController::class, 'download'])->name('videos.download');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

// Python stream route (no auth required)
Route::get('/python/stream/{camera_id}', function ($camera_id) {
    // This route is just a placeholder, the actual stream will be handled by Python
    abort(404);
})->name('python.stream');

require __DIR__.'/auth.php';
