<?php

// app/Http/Controllers/CameraController.php
namespace App\Http\Controllers;

use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CameraController extends Controller
{
    public function index()
    {
        $cameras = Camera::all();
        return view('cameras.index', compact('cameras'));
    }

    public function create()
    {
        return view('cameras.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:usb,ip',
            'device_id' => 'required_if:type,usb|nullable|string',
            'rtsp_url' => 'required_if:type,ip|nullable|url',
            'is_active' => 'boolean',
        ]);

        // Generate stream URL (will be updated by Python service)
        $validated['stream_url'] = route('python.stream', ['camera_id' => 'new']);

        $camera = Camera::create($validated);

        // Notify Python service about new camera
        $this->notifyPythonService($camera, 'add');

        return redirect()->route('cameras.index')->with('success', 'Camera added successfully.');
    }

    public function show(Camera $camera)
    {
        return view('cameras.show', compact('camera'));
    }

    public function edit(Camera $camera)
    {
        return view('cameras.edit', compact('camera'));
    }

    public function update(Request $request, Camera $camera)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:usb,ip',
            'device_id' => 'required_if:type,usb|nullable|string',
            'rtsp_url' => 'required_if:type,ip|nullable|url',
            'is_active' => 'boolean',
        ]);

        $camera->update($validated);

        // Notify Python service about updated camera
        $this->notifyPythonService($camera, 'update');

        return redirect()->route('cameras.index')->with('success', 'Camera updated successfully.');
    }

    public function destroy(Camera $camera)
    {
        // Notify Python service about camera removal
        $this->notifyPythonService($camera, 'remove');

        $camera->delete();

        return redirect()->route('cameras.index')->with('success', 'Camera deleted successfully.');
    }

    protected function notifyPythonService(Camera $camera, $action)
    {
        try {
            $pythonServiceUrl = config('app.python_service_url');

            if ($action === 'add' || $action === 'update') {
                Http::post("$pythonServiceUrl/api/camera/$action", [
                    'camera_id' => $camera->id,
                    'name' => $camera->name,
                    'type' => $camera->type,
                    'device_id' => $camera->device_id,
                    'rtsp_url' => $camera->rtsp_url,
                    'is_active' => $camera->is_active,
                ]);
            } elseif ($action === 'remove') {
                Http::post("$pythonServiceUrl/api/camera/remove", [
                    'camera_id' => $camera->id,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to notify Python service about camera $action: " . $e->getMessage());
        }
    }
}
