@extends('layouts.app')

@section('title', 'Add Known Face')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Add Known Face') }}
    </h2>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <form method="POST" action="{{ route('faces.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="name" name="name" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="tag" class="block text-sm font-medium text-gray-700">Tag</label>
                        <input type="text" id="tag" name="tag" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <p class="mt-2 text-sm text-gray-500">Optional category or group for this person</p>
                    </div>

                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                        <div class="mt-1 flex items-center">
                            <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            <input type="file" id="photo" name="photo" accept="image/*" required class="ml-5 py-2 px-3 border border-gray-300 rounded-md text-sm leading-4 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Clear frontal photo of the face for best recognition</p>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Add Face
                    </button>
                    <a href="{{ route('faces.index') }}" class="ml-2 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
