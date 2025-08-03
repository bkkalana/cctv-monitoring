@extends('layouts.app')

@section('title', $face->name)

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $face->name }}
    </h2>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <div class="h-64 w-full bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                        <img src="{{ Storage::url($face->photo_path) }}" alt="{{ $face->name }}" class="h-full w-full object-cover">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Face Details</h3>

                    <div class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Name:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $face->name }}</span>
                        </div>

                        @if($face->tag)
                            <div>
                                <span class="text-sm font-medium text-gray-500">Tag:</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $face->tag }}
                            </span>
                            </div>
                        @endif

                        <div>
                            <span class="text-sm font-medium text-gray-500">Face ID:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $face->id }}</span>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500">Added:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $face->created_at->format('M j, Y g:i A') }}</span>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500">Last Updated:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $face->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('faces.edit', $face) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Edit Face
                        </a>
                        @can('delete faces')
                            <form action="{{ route('faces.destroy', $face) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Are you sure you want to delete this face?')">
                                    Delete Face
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Alerts</h3>

            @if($face->alerts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($face->alerts->take(6) as $alert)
                        <div class="border rounded-lg p-3 hover:bg-gray-50">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-16 w-16 overflow-hidden rounded-md border">
                                    <img src="{{ Storage::url($alert->snapshot_path) }}" alt="Alert Snapshot" class="h-full w-full object-cover">
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $alert->camera->name }}
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
                    <a href="{{ route('alerts.index') }}?face={{ $face->id }}" class="text-sm text-blue-600 hover:text-blue-800">View All Alerts</a>
                </div>
            @else
                <p class="text-gray-500">No alerts for this face.</p>
            @endif
        </div>
    </div>
@endsection
