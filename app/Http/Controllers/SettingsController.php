<?php

// app/Http/Controllers/SettingsController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'email_alerts_enabled' => Setting::getValue('email_alerts_enabled', false),
            'alert_email_recipients' => Setting::getValue('alert_email_recipients', ''),
            'smtp_host' => Setting::getValue('smtp_host', ''),
            'smtp_port' => Setting::getValue('smtp_port', '587'),
            'smtp_username' => Setting::getValue('smtp_username', ''),
            'smtp_password' => Setting::getValue('smtp_password', ''),
            'smtp_encryption' => Setting::getValue('smtp_encryption', 'tls'),
            'video_retention_days' => Setting::getValue('video_retention_days', '30'),
            'face_detection_confidence' => Setting::getValue('face_detection_confidence', '0.8'),
            'python_service_url' => Setting::getValue('python_service_url', 'http://localhost:5000'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'email_alerts_enabled' => 'boolean',
            'alert_email_recipients' => 'nullable|string',
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|integer',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'nullable|string|in:tls,ssl',
            'video_retention_days' => 'required|integer|min:1',
            'face_detection_confidence' => 'required|numeric|min:0.1|max:1',
            'python_service_url' => 'required|url',
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value);
        }

        // Update Python service settings
        $this->updatePythonSettings($validated);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    protected function updatePythonSettings($settings)
    {
        try {
            $pythonServiceUrl = $settings['python_service_url'];

            Http::post("$pythonServiceUrl/api/settings/update", [
                'face_detection_confidence' => $settings['face_detection_confidence'],
                'video_retention_days' => $settings['video_retention_days'],
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to update Python service settings: " . $e->getMessage());
        }
    }
}
