<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Event Planner') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:600,700|manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @php($hasViteAssets = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @if ($hasViteAssets)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased text-slate-900">
        <div class="app-surface">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="px-4 pt-6 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-6xl app-panel px-6 py-5 sm:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="px-4 pb-12 pt-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
