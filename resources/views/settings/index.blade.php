@extends('layouts.app')

@section('title', 'Settings')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('System Settings') }}
    </h2>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6">
                    <!-- Email Alerts Section -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Email Alerts</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email_alerts_enabled" class="block text-sm font-medium text-gray-700">Enable Email Alerts</label>
                                <select id="email_alerts_enabled" name="email_alerts_enabled" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="1" {{ $settings['email_alerts_enabled'] ? 'selected' : '' }}>Enabled</option>
                                    <option value="0" {{ !$settings['email_alerts_enabled'] ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>

                            <div>
                                <label for="alert_email_recipients" class="block text-sm font-medium text-gray-700">Recipients (comma separated)</label>
                                <input type="text" id="alert_email_recipients" name="alert_email_recipients" value="{{ $settings['alert_email_recipients'] }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- SMTP Settings Section -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">SMTP Settings</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="smtp_host" class="block text-sm font-medium text-gray-700">SMTP Host</label>
                                <input type="text" id="smtp_host" name="smtp_host" value="{{ $settings['smtp_host'] }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div>
                                <label for="smtp_port" class="block text-sm font-medium text-gray-700">SMTP Port</label>
                                <input type="text" id="smtp_port" name="smtp_port" value="{{ $settings['smtp_port'] }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div>
                                <label for="smtp_username" class="block text-sm font-medium text-gray-700">SMTP Username</label>
                                <input type="text" id="smtp_username" name="smtp_username" value="{{ $settings['smtp_username'] }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div>
                                <label for="smtp_password" class="block text-sm font-medium text-gray-700">SMTP Password</label>
                                <input type="password" id="smtp_password" name="smtp_password" value="{{ $settings['smtp_password'] }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div>
                                <label for="smtp_encryption" class="block text-sm font-medium text-gray-700">Encryption</label>
                                <select id="smtp_encryption" name="smtp_encryption" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="tls" {{ $settings['smtp_encryption'] === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ $settings['smtp_encryption'] === 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Video Settings Section -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Video Settings</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="video_retention_days" class="block text-sm font-medium text-gray-700">Video Retention (days)</label>
                                <input type="number" id="video_retention_days" name="video_retention_days" value="{{ $settings['video_retention_days'] }}" min="1" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- Face Detection Settings Section -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Face Detection Settings</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="face_detection_confidence" class="block text-sm font-medium text-gray-700">Confidence Threshold (0.1-1.0)</label>
                                <input type="number" id="face_detection_confidence" name="face_detection_confidence" value="{{ $settings['face_detection_confidence'] }}" min="0.1" max="1.0" step="0.05" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- Python Service Settings Section -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Python Service Settings</h3>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="python_service_url" class="block text-sm font-medium text-gray-700">Python Service URL</label>
                                <input type="url" id="python_service_url" name="python_service_url" value="{{ $settings['python_service_url'] }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-2 text-sm text-gray-500">URL where the Python face detection service is running (e.g., http://localhost:5000)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
