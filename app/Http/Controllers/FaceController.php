<?php

// app/Http/Controllers/FaceController.php
namespace App\Http\Controllers;

use App\Models\Face;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class FaceController extends Controller
{
    public function index()
    {
        $faces = Face::all();
        return view('faces.index', compact('faces'));
    }

    public function create()
    {
        return view('faces.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tag' => 'nullable|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Store the uploaded photo
        $path = $request->file('photo')->store('faces', 'public');
        $validated['photo_path'] = $path;

        $face = Face::create($validated);

        // Sync face data with Python service
        $this->syncFaceWithPython($face, 'add');

        return redirect()->route('faces.index')->with('success', 'Face added successfully.');
    }

    public function show(Face $face)
    {
        return view('faces.show', compact('face'));
    }

    public function edit(Face $face)
    {
        return view('faces.edit', compact('face'));
    }

    public function update(Request $request, Face $face)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tag' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            Storage::disk('public')->delete($face->photo_path);

            // Store new photo
            $path = $request->file('photo')->store('faces', 'public');
            $validated['photo_path'] = $path;
        }

        $face->update($validated);

        // Sync face data with Python service
        $this->syncFaceWithPython($face, 'update');

        return redirect()->route('faces.index')->with('success', 'Face updated successfully.');
    }

    public function destroy(Face $face)
    {
        // Sync face data with Python service
        $this->syncFaceWithPython($face, 'remove');

        // Delete photo
        Storage::disk('public')->delete($face->photo_path);

        $face->delete();

        return redirect()->route('faces.index')->with('success', 'Face deleted successfully.');
    }

    protected function syncFaceWithPython(Face $face, $action)
    {
        try {
            $pythonServiceUrl = config('app.python_service_url');
            $photoUrl = Storage::disk('public')->url($face->photo_path);

            if ($action === 'add' || $action === 'update') {
                Http::post("$pythonServiceUrl/api/face/$action", [
                    'face_id' => $face->id,
                    'name' => $face->name,
                    'tag' => $face->tag,
                    'photo_url' => $photoUrl,
                ]);
            } elseif ($action === 'remove') {
                Http::post("$pythonServiceUrl/api/face/remove", [
                    'face_id' => $face->id,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to sync face with Python service: " . $e->getMessage());
        }
    }
}
