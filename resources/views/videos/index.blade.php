@extends('layouts.app')

@section('title', 'Videos')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Recorded Videos') }}
    </h2>
@endsection

@section('content')


    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Camera
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Recording Time
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Duration
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Trigger
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($videos as $video)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $video->camera->name }}</div>
                                <div class="text-sm text-gray-500">{{ $video->camera->type === 'usb' ? 'USB Camera' : 'IP Camera' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $video->start_time->format('M j, Y g:i A') }}</div>
                        <div class="text-sm text-gray-500">to {{ $video->end_time->format('g:i A') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $video->duration }} minutes
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $video->trigger_type === 'scheduled' ? 'blue' : 'red' }}-100 text-{{ $video->trigger_type === 'scheduled' ? 'blue' : 'red' }}-800">
                                {{ ucfirst($video->trigger_type) }}
                            </span>
                        @if($video->alert)
                            <div class="text-xs text-gray-500 mt-1">Alert #{{ $video->alert->id }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('videos.show', $video) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                        <a href="{{ route('videos.download', $video) }}" class="text-green-600 hover:text-green-900 mr-3">Download</a>
                        @can('delete videos')
                            <form action="{{ route('videos.destroy', $video) }}" method="POST" class="inline">
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
                        No videos found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($videos->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $videos->links() }}
            </div>
        @endif
    </div>
@endsection
