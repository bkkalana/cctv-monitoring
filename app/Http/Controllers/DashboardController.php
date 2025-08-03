<?php

// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\Alert;
use App\Models\Video;

class DashboardController extends Controller
{
    public function index()
    {
        $cameras = Camera::all();
        $recentAlerts = Alert::with(['camera', 'face'])->latest()->take(5)->get();
        $recentVideos = Video::with('camera')->latest()->take(5)->get();

        $stats = [
            'total_cameras' => $cameras->count(),
            'online_cameras' => $cameras->where('is_online', true)->count(),
            'total_alerts' => Alert::count(),
            'today_alerts' => Alert::whereDate('created_at', today())->count(),
            'total_videos' => Video::count(),
        ];

        return view('dashboard', compact('cameras', 'recentAlerts', 'recentVideos', 'stats'));
    }
}
