@extends('layouts.app')

@section('title', 'Alerts')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Alerts') }}
    </h2>
@endsection

@section('content')
    <div class="mb-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Search alerts..." class="border rounded px-3 py-2 w-64">

            <select class="border rounded px-3 py-2">
                <option>All Types</option>
                <option>Unknown Faces</option>
                <option>Recognized Faces</option>
            </select>

            <select class="border rounded px-3 py-2">
                <option>All Cameras</option>
                @foreach($cameras as $camera)
                    <option value="{{ $camera->id }}">{{ $camera->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Export
            </button>
        </div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Snapshot
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Details
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Camera
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Time
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($alerts as $alert)
                <tr>
                    <td class="px-6 py-4">
                        <div class="h-16 w-16 overflow-hidden rounded-md border">
                            <img src="{{ Storage::url($alert->snapshot_path) }}" alt="Alert Snapshot" class="h-full w-full object-cover">
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">
                            @if($alert->is_recognized && $alert->face)
                                Recognized: {{ $alert->face->name }}
                                @if($alert->face->tag)
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full ml-2">{{ $alert->face->tag }}</span>
                                @endif
                            @else
                                Unknown Person Detected
                            @endif
                        </div>
                        @if($alert->confidence)
                            <div class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $alert->confidence > 0.7 ? 'green' : ($alert->confidence > 0.4 ? 'yellow' : 'red') }}-100 text-{{ $alert->confidence > 0.7 ? 'green' : ($alert->confidence > 0.4 ? 'yellow' : 'red') }}-800">
                                        Confidence: {{ number_format($alert->confidence * 100, 1) }}%
                                    </span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $alert->camera->name }}</div>
                        <div class="text-sm text-gray-500">{{ $alert->camera->type === 'usb' ? 'USB Camera' : 'IP Camera' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $alert->created_at->format('M j, Y g:i A') }}
                        <div class="text-xs text-gray-400">{{ $alert->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('alerts.show', $alert) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                        @can('delete alerts')
                            <form action="{{ route('alerts.destroy', $alert) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        No alerts found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($alerts->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>
@endsection
