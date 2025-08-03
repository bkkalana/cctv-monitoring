@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stats Cards -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Cameras</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_cameras'] }}</p>
                    <p class="text-sm text-gray-500">{{ $stats['online_cameras'] }} online</p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Alerts</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_alerts'] }}</p>
                    <p class="text-sm text-gray-500">{{ $stats['today_alerts'] }} today</p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Recorded Videos</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_videos'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">System Status</h3>
                    <p class="text-2xl font-semibold text-gray-900">Operational</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Camera Streams -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Live Camera Streams</h3>

            @if($cameras->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($cameras as $camera)
                        <div class="border rounded-lg overflow-hidden">
                            <div class="bg-gray-800 p-2 flex justify-between items-center">
                                <h4 class="text-white font-medium">{{ $camera->name }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full {{ $camera->is_online ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $camera->is_online ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                            <div class="bg-black h-48 flex items-center justify-center">
                                @if($camera->is_online && $camera->stream_url)
                                    <img src="{{ $camera->stream_url }}" alt="{{ $camera->name }}" class="max-h-full max-w-full">
                                @else
                                    <div class="text-white">Camera Offline</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No cameras configured. Add cameras to view live streams.</p>
            @endif
        </div>
    </div>

    <!-- Recent Alerts and Videos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Alerts -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Recent Alerts</h3>
                    <a href="{{ route('alerts.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>

                @if($recentAlerts->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentAlerts as $alert)
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
                                            {{ $alert->camera->name }} • {{ $alert->created_at->diffForHumans() }}
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
                @else
                    <p class="text-gray-500">No recent alerts.</p>
                @endif
            </div>
        </div>

        <!-- Recent Videos -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Recent Videos</h3>
                    <a href="{{ route('videos.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>

                @if($recentVideos->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentVideos as $video)
                            <div class="border rounded-lg p-3 hover:bg-gray-50">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-16 w-16 bg-gray-200 rounded-md flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $video->camera->name }} Recording
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $video->start_time->format('M j, Y g:i A') }} •
                                            {{ $video->duration }} minutes
                                        </p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($video->trigger_type) }}
                                        </span>
                                    </div>
                                    <div class="ml-auto">
                                        <a href="{{ route('videos.download', $video) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Download</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No recent videos.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Refresh camera streams every 5 seconds
        setInterval(() => {
            document.querySelectorAll('img[src^="{{ route('python.stream') }}"]').forEach(img => {
                img.src = img.src.split('?')[0] + '?t=' + new Date().getTime();
            });
        }, 5000);
    </script>
@endpush
