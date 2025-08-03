@extends('layouts.app')

@section('title', 'Cameras')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Cameras') }}
    </h2>
@endsection

@section('content')
    <div class="mb-4 flex justify-between items-center">
        <div class="flex-1">
            <input type="text" placeholder="Search cameras..." class="border rounded px-3 py-2 w-64">
        </div>
        @can('create cameras')
            <a href="{{ route('cameras.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Camera
            </a>
        @endcan
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Last Activity
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($cameras as $camera)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('cameras.show', $camera) }}" class="hover:text-blue-600">{{ $camera->name }}</a>
                                </div>
                                <div class="text-sm text-gray-500">
                                    @if($camera->type === 'usb')
                                        USB: {{ $camera->device_id }}
                                    @else
                                        IP: {{ $camera->rtsp_url }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $camera->type === 'usb' ? 'blue' : 'purple' }}-100 text-{{ $camera->type === 'usb' ? 'blue' : 'purple' }}-800">
                                {{ strtoupper($camera->type) }}
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $camera->is_online ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $camera->is_online ? 'Online' : 'Offline' }}
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $camera->updated_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('cameras.edit', $camera) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        @can('delete cameras')
                            <form action="{{ route('cameras.destroy', $camera) }}" method="POST" class="inline">
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
                        No cameras found. <a href="{{ route('cameras.create') }}" class="text-blue-600 hover:text-blue-800">Add one now</a>.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
