<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::group([],function () {
    // API for Python service to create alerts
    Route::post('/alerts', [AlertController::class, 'storeFromPython']);

    // API for Python service to create video records
    Route::post('/videos', [VideoController::class, 'storeFromPython']);

    // API to get camera list for Python service
    Route::get('/cameras', function () {
        return response()->json([
            'cameras' => \App\Models\Camera::where('is_active', true)->get(),
        ]);
    });

    // API to get face dataset for Python service
    Route::get('/faces', function () {
        return response()->json([
            'faces' => \App\Models\Face::all()->map(function ($face) {
                return [
                    'id' => $face->id,
                    'name' => $face->name,
                    'tag' => $face->tag,
                    'photo_url' => \Illuminate\Support\Facades\Storage::disk('public')->url($face->photo_path),
                    'encodings' => $face->encodings,
                ];
            }),
        ]);
    });
});
