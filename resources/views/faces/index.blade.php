@extends('layouts.app')

@section('title', 'Known Faces')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Known Faces') }}
    </h2>
@endsection

@section('content')
    <div class="mb-4 flex justify-between items-center">
        @can('create faces')
        <a href="{{ route('faces.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add Face
        </a>
        @endcan
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Photo
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Details
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tag
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Last Updated
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($faces as $face)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="h-16 w-16 overflow-hidden rounded-md border">
                                <img src="{{ Storage::url($face->photo_path) }}" alt="{{ $face->name }}" class="h-full w-full object-cover">
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $face->name }}</div>
                            <div class="text-sm text-gray-500">ID: {{ $face->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($face->tag)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $face->tag }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500">None</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $face->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('faces.show', $face) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="{{ route('faces.edit', $face) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            @can('delete faces')
                            <form action="{{ route('faces.destroy', $face) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this face?')">Delete</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No known faces found. <a href="{{ route('faces.create') }}" class="text-blue-600 hover:text-blue-800">Add one now</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
