<?php

// app/Http/Controllers/AlertController.php
namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertNotification;
use App\Models\Setting;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::with(['camera', 'face'])->latest()->paginate(20);
        return view('alerts.index', compact('alerts'));
    }

    public function show(Alert $alert)
    {
        return view('alerts.show', compact('alert'));
    }

    public function destroy(Alert $alert)
    {
        // Delete snapshot file
        if (Storage::disk('public')->exists($alert->snapshot_path)) {
            Storage::disk('public')->delete($alert->snapshot_path);
        }

        $alert->delete();

        return redirect()->route('alerts.index')->with('success', 'Alert deleted successfully.');
    }

    // API endpoint for Python service to create alerts
    public function storeFromPython(Request $request)
    {
        $validated = $request->validate([
            'camera_id' => 'required|exists:cameras,id',
            'snapshot' => 'required|string', // base64 encoded image
            'confidence' => 'nullable|numeric',
            'face_id' => 'nullable|exists:faces,id',
        ]);

        // Save the snapshot image
        $imageData = base64_decode($validated['snapshot']);
        $fileName = 'alerts/' . uniqid() . '.jpg';
        Storage::disk('public')->put($fileName, $imageData);

        // Create the alert
        $alert = Alert::create([
            'camera_id' => $validated['camera_id'],
            'snapshot_path' => $fileName,
            'confidence' => $validated['confidence'] ?? null,
            'type' => $validated['face_id'] ? 'known_face' : 'unknown_face',
            'is_recognized' => (bool)$validated['face_id'],
            'face_id' => $validated['face_id'] ?? null,
        ]);

        // Send email notification if enabled
        if (Setting::getValue('email_alerts_enabled', false)) {
            $this->sendAlertEmail($alert);
        }

        return response()->json(['success' => true, 'alert_id' => $alert->id]);
    }

    protected function sendAlertEmail(Alert $alert)
    {
        $recipients = explode(',', Setting::getValue('alert_email_recipients', ''));

        foreach ($recipients as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->send(new AlertNotification($alert));
            }
        }
    }
}
