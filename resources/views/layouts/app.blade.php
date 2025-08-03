<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
<div class="min-h-screen">
    @include('layouts.navigation')

    <!-- Page Heading -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @yield('header')
        </div>
    </header>

    <!-- Page Content -->
    <main>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 px-4 py-2 bg-red-100 border border-red-200 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>
