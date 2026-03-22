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
        <div class="app-surface px-4 py-8 sm:px-6">
            <div class="mx-auto w-full max-w-md">
                <a href="{{ route('home') }}" class="mx-auto flex w-fit items-center gap-3">
                    <x-application-logo class="h-14 w-14" />
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-orange-600">Event Planner</p>
                        <p class="text-2xl text-slate-950">Event Planner</p>
                    </div>
                </a>

                <div class="mt-6 app-panel px-6 py-6 sm:px-8 sm:py-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
