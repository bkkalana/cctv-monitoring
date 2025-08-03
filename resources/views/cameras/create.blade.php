@extends('layouts.app')

@section('title', 'Add Camera')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Add Camera') }}
    </h2>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <form method="POST" action="{{ route('cameras.store') }}">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Camera Name</label>
                        <input type="text" id="name" name="name" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Camera Type</label>
                        <select id="type" name="type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="usb">USB Camera</option>
                            <option value="ip">IP Camera (RTSP)</option>
                        </select>
                    </div>

                    <div id="usb-field">
                        <label for="device_id" class="block text-sm font-medium text-gray-700">USB Device ID</label>
                        <input type="text" id="device_id" name="device_id" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <p class="mt-2 text-sm text-gray-500">Typically 0, 1, 2, etc. depending on connected devices</p>
                    </div>

                    <div id="ip-field" class="hidden">
                        <label for="rtsp_url" class="block text-sm font-medium text-gray-700">RTSP URL</label>
                        <input type="text" id="rtsp_url" name="rtsp_url" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="rtsp://username:password@ip:port/path">
                    </div>

                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="is_active" name="is_active" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Add Camera
                    </button>
                    <a href="{{ route('cameras.index') }}" class="ml-2 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('type').addEventListener('change', function() {
            const type = this.value;
            document.getElementById('usb-field').classList.toggle('hidden', type !== 'usb');
            document.getElementById('ip-field').classList.toggle('hidden', type !== 'ip');
        });
    </script>
@endpush
