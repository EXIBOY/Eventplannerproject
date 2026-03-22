<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Event Planner') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:600,700|manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        @php($hasViteAssets = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @if ($hasViteAssets)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased text-slate-900">
        <div class="app-surface">
            <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-4 py-6 sm:px-6 lg:px-8">
                <header class="rounded-[30px] border border-white/80 bg-white/75 px-5 py-4 shadow-[0_24px_70px_rgba(15,23,42,0.08)] backdrop-blur sm:px-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('home') }}" class="flex items-center gap-3">
                            <x-application-logo class="h-12 w-12" />
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.35em] text-orange-600">Event Planner</p>
                                <p class="text-2xl text-slate-950">Event Planner</p>
                            </div>
                        </a>

                        <div class="flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-primary">
                                    Open Dashboard
                                </a>
                            @else
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="btn-secondary">
                                        Log In
                                    </a>
                                @endif

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-primary">
                                        Start Planning
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </header>

                <main class="flex-1 py-8 lg:py-12">
                    <section class="grid gap-6 lg:grid-cols-[1.08fr_0.92fr]">
                        <div class="app-panel mesh-accent px-7 py-8 sm:px-10 sm:py-10">
                            <span class="section-label">Event operations without the chaos</span>

                            <h1 class="mt-5 max-w-3xl text-5xl leading-[0.94] text-slate-950 sm:text-6xl">
                                Plan every detail. Keep every date moving.
                            </h1>

                            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                                Event Planner gives event teams one place to track launches, workshops, dinners, and internal planning milestones without juggling spreadsheets and chat threads.
                            </p>

                            <div class="mt-8 flex flex-wrap gap-3">
                                @auth
                                    <a href="{{ route('events.index') }}" class="btn-primary">
                                        View Events
                                    </a>
                                @else
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn-primary">
                                            Create Your Workspace
                                        </a>
                                    @endif

                                    @if (Route::has('login'))
                                        <a href="{{ route('login') }}" class="btn-secondary">
                                            Use Existing Account
                                        </a>
                                    @endif
                                @endauth
                            </div>

                            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                                <div class="stat-card">
                                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Schedules</p>
                                    <p class="mt-3 text-3xl font-semibold text-slate-950">Live</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">Keep your upcoming and completed events in one timeline.</p>
                                </div>
                                <div class="stat-card">
                                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Planning</p>
                                    <p class="mt-3 text-3xl font-semibold text-slate-950">Focused</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">Capture the title, location, date, and brief without friction.</p>
                                </div>
                                <div class="stat-card">
                                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Dashboards</p>
                                    <p class="mt-3 text-3xl font-semibold text-slate-950">Clear</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">See what is next, what is done, and what still needs attention.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="app-panel px-7 py-8 sm:px-8">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Sample Schedule</p>
                                        <h2 class="mt-2 text-3xl text-slate-950">This week at a glance</h2>
                                    </div>

                                    <span class="event-tag">3 active briefs</span>
                                </div>

                                <div class="mt-8 space-y-4">
                                    <div class="event-card bg-slate-950 text-white">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-orange-300">Mon 24</p>
                                                <h3 class="mt-2 text-2xl text-white">Brand Launch Walkthrough</h3>
                                                <p class="mt-3 text-sm leading-6 text-white/70">Final rooming check, AV sign-off, and guest arrival sequencing for the evening launch.</p>
                                            </div>
                                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/80">London</span>
                                        </div>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="stat-card">
                                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Wed 26</p>
                                            <h3 class="mt-3 text-2xl text-slate-950">Founder Breakfast</h3>
                                            <p class="mt-2 text-sm leading-6 text-slate-600">Guest confirmations, seating notes, and dietary follow-up.</p>
                                        </div>
                                        <div class="stat-card">
                                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Fri 28</p>
                                            <h3 class="mt-3 text-2xl text-slate-950">Workshop Prep</h3>
                                            <p class="mt-2 text-sm leading-6 text-slate-600">Agenda lock, facilitator notes, and participant packs.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="app-panel px-6 py-6">
                                    <span class="section-label">Why it works</span>
                                    <p class="mt-4 text-2xl text-slate-950">One event list, no dead ends.</p>
                                    <p class="mt-3 text-sm leading-7 text-slate-600">Create, update, and archive events from one workflow instead of piecing details together across multiple tabs.</p>
                                </div>
                                <div class="app-panel px-6 py-6">
                                    <span class="section-label">Built for teams</span>
                                    <p class="mt-4 text-2xl text-slate-950">Fast enough for daily use.</p>
                                    <p class="mt-3 text-sm leading-7 text-slate-600">The dashboard surfaces upcoming work immediately, so the next decision is always in front of you.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="mt-8 grid gap-4 lg:grid-cols-3">
                        <div class="app-panel px-6 py-6">
                            <span class="section-label">Capture</span>
                            <h2 class="mt-4 text-3xl text-slate-950">Brief each event clearly</h2>
                            <p class="mt-3 text-sm leading-7 text-slate-600">Store the essentials for each event with clean forms and validation that keeps records consistent.</p>
                        </div>
                        <div class="app-panel px-6 py-6">
                            <span class="section-label">Track</span>
                            <h2 class="mt-4 text-3xl text-slate-950">See upcoming work instantly</h2>
                            <p class="mt-3 text-sm leading-7 text-slate-600">The dashboard separates what is next from what is already completed, including events happening today.</p>
                        </div>
                        <div class="app-panel px-6 py-6">
                            <span class="section-label">Refine</span>
                            <h2 class="mt-4 text-3xl text-slate-950">Update plans without risk</h2>
                            <p class="mt-3 text-sm leading-7 text-slate-600">Each event is tied to its owner so users can only manage their own schedule and records.</p>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </body>
</html>
