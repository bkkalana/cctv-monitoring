@extends('layouts.app')

@section('title', $camera->name)

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $camera->name }}
    </h2>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Camera Details</h3>

                    <div class="space-y-2">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Name:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $camera->name }}</span>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500">Type:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $camera->type === 'usb' ? 'blue' : 'purple' }}-100 text-{{ $camera->type === 'usb' ? 'blue' : 'purple' }}-800">
                                {{ strtoupper($camera->type) }}
                            </span>
                        </div>

                        @if($camera->type === 'usb')
                            <div>
                                <span class="text-sm font-medium text-gray-500">Device ID:</span>
                                <span class="text-sm text-gray-900 ml-2">{{ $camera->device_id }}</span>
                            </div>
                        @else
                            <div>
                                <span class="text-sm font-medium text-gray-500">RTSP URL:</span>
                                <span class="text-sm text-gray-900 ml-2">{{ $camera->rtsp_url }}</span>
                            </div>
                        @endif

                        <div>
                            <span class="text-sm font-medium text-gray-500">Status:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $camera->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $camera->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500">Connection:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $camera->is_online ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $camera->is_online ? 'Online' : 'Offline' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500">Last Updated:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $camera->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="mt-4 flex space-x-3">
                        <a href="{{ route('cameras.edit', $camera) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Edit Camera
                        </a>
                        @can('delete cameras')
                            <form action="{{ route('cameras.destroy', $camera) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Are you sure you want to delete this camera?')">
                                    Delete Camera
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Live Stream</h3>

                    <div class="bg-black rounded-md overflow-hidden">
                        @if($camera->is_online && $camera->stream_url)
                            <img src="{{ $camera->stream_url }}" alt="{{ $camera->name }} Stream" class="w-full h-64 object-contain">
                        @else
                            <div class="h-64 flex items-center justify-center text-white">
                                Camera is offline
                            </div>
                        @endif
                    </div>

                    <div class="mt-2 flex justify-center">
                        <button class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Alerts</h3>

                @if($camera->alerts->count() > 0)
                    <div class="space-y-4">
                        @foreach($camera->alerts->take(5) as $alert)
                            <div class="border rounded-lg p-3 hover:bg-gray-50">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-16 w-16 overflow-hidden rounded-md border">
                                        <img src="{{ Storage::url($alert->snapshot_path) }}" alt="Alert Snapshot" class="h-full w-full object-cover">
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $alert->is_recognized && $alert->face ? 'Recognized: ' . $alert->face->name : 'Unknown Person Detected' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $alert->created_at->diffForHumans() }}
                                        </p>
                                        @if($alert->confidence)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $alert->confidence > 0.7 ? 'green' : ($alert->confidence > 0.4 ? 'yellow' : 'red') }}-100 text-{{ $alert->confidence > 0.7 ? 'green' : ($alert->confidence > 0.4 ? 'yellow' : 'red') }}-800">
                                                Confidence: {{ number_format($alert->confidence * 100, 1) }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 text-right">
                        <a href="{{ route('alerts.index') }}?camera={{ $camera->id }}" class="text-sm text-blue-600 hover:text-blue-800">View All Alerts</a>
                    </div>
                @else
                    <p class="text-gray-500">No alerts for this camera.</p>
                @endif
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Recordings</h3>

                @if($camera->videos->count() > 0)
                    <div class="space-y-4">
                        @foreach($camera->videos->take(5) as $video)
                            <div class="border rounded-lg p-3 hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-16 w-16 bg-gray-200 rounded-md flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $video->start_time->format('M j, Y g:i A') }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Duration: {{ $video->duration }} minutes
                                        </p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($video->trigger_type) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('videos.download', $video) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Download</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 text-right">
                        <a href="{{ route('videos.index') }}?camera={{ $camera->id }}" class="text-sm text-blue-600 hover:text-blue-800">View All Recordings</a>
                    </div>
                @else
                    <p class="text-gray-500">No recordings for this camera.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
