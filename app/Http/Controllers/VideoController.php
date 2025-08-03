<?php

// app/Http/Controllers/VideoController.php
namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::with(['camera', 'alert'])->latest()->paginate(20);
        return view('videos.index', compact('videos'));
    }

    public function show(Video $video)
    {
        return view('videos.show', compact('video'));
    }

    public function download(Video $video)
    {
        if (!Storage::disk('public')->exists($video->path)) {
            abort(404);
        }

        return Storage::disk('public')->download($video->path);
    }

    public function destroy(Video $video)
    {
        if (Storage::disk('public')->exists($video->path)) {
            Storage::disk('public')->delete($video->path);
        }

        $video->delete();

        return redirect()->route('videos.index')->with('success', 'Video deleted successfully.');
    }

    // API endpoint for Python service to create video records
    public function storeFromPython(Request $request)
    {
        $validated = $request->validate([
            'camera_id' => 'required|exists:cameras,id',
            'file_path' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'trigger_type' => 'required|in:scheduled,alert',
            'alert_id' => 'nullable|exists:alerts,id',
        ]);

        $video = Video::create([
            'camera_id' => $validated['camera_id'],
            'path' => $validated['file_path'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'trigger_type' => $validated['trigger_type'],
            'alert_id' => $validated['alert_id'] ?? null,
        ]);

        return response()->json(['success' => true, 'video_id' => $video->id]);
    }
}
