<x-app-layout>
    <div class="mx-auto max-w-6xl space-y-6">
        <section class="app-panel mesh-accent overflow-hidden px-5 py-6 sm:px-8 sm:py-8 lg:px-10 lg:py-10">
            <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
                <div>
                    <span class="section-label">Control Room</span>
                    <h1 class="page-hero-title mt-5 text-slate-950">
                        Keep your event calendar sharp and visible.
                    </h1>
                    <p class="lede-copy mt-5 max-w-2xl text-slate-600">
                        Review the pipeline, spot the next milestone, and update your event schedule before details slip.
                    </p>

                    <div class="action-row mt-8">
                        <a href="{{ route('events.create') }}" class="btn-primary">
                            Add New Event
                        </a>
                        <a href="{{ route('events.index') }}" class="btn-secondary">
                            Review All Events
                        </a>
                    </div>
                </div>

                <div class="event-card bg-slate-950 text-white">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-orange-300">Next Up</p>

                    @if ($nextEvent)
                        <h2 class="page-title mt-4 text-white">{{ $nextEvent->title }}</h2>
                        <p class="mt-4 text-sm uppercase tracking-[0.22em] text-white/55">
                            {{ $nextEvent->event_date->format('l, d M Y') }}
                        </p>
                        <p class="mt-3 text-base text-white/80">{{ $nextEvent->location }}</p>
                        <p class="mt-5 text-sm leading-7 text-white/70">
                            {{ $nextEvent->description ?: 'No description added yet. Open the event to capture the brief, stakeholders, or production notes.' }}
                        </p>
                    @else
                        <h2 class="mt-4 text-4xl text-white">No upcoming events yet</h2>
                        <p class="mt-5 text-sm leading-7 text-white/70">
                            Start with one event brief and the dashboard will begin tracking your active schedule.
                        </p>
                    @endif
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="stat-card">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Total Events</p>
                <p class="metric-number mt-4 text-slate-950">{{ $eventCount }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">Every active and completed event in your workspace.</p>
            </div>

            <div class="stat-card">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Upcoming</p>
                <p class="metric-number mt-4 text-slate-950">{{ $upcomingEvents }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">Includes events scheduled for today and all future dates.</p>
            </div>

            <div class="stat-card">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Recent Activity</p>
                <p class="metric-number mt-4 text-slate-950">{{ $recentActivity->count() }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">Your latest completed events, kept handy for quick reference.</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="app-panel px-5 py-6 sm:px-8 sm:py-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="section-label">Schedule</span>
                        <h2 class="page-title mt-4 text-slate-950">Upcoming timeline</h2>
                    </div>

                    <a href="{{ route('events.index') }}" class="btn-secondary">
                        Manage Events
                    </a>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($upcomingSchedule->take(4) as $event)
                        <div class="event-card">
                            <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                    <div class="shrink-0 self-start rounded-[22px] bg-orange-100 px-4 py-3 text-center text-orange-700">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em]">{{ $event->event_date->format('M') }}</p>
                                        <p class="mt-1 text-3xl font-semibold text-orange-900">{{ $event->event_date->format('d') }}</p>
                                    </div>

                                    <div>
                                        <h3 class="card-title text-slate-950">{{ $event->title }}</h3>
                                        <p class="mt-2 text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">
                                            {{ $event->event_date->format('l') }} · {{ $event->location }}
                                        </p>
                                        <p class="mt-3 text-sm leading-7 text-slate-600">
                                            {{ $event->description ?: 'No summary added yet. Open the event to complete the planning brief.' }}
                                        </p>
                                    </div>
                                </div>

                                <a href="{{ route('events.edit', $event) }}" class="btn-secondary">
                                    Edit
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="event-card text-center">
                            <h3 class="text-3xl text-slate-950">Nothing on the calendar yet</h3>
                            <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-600">
                                Create your first event to populate the timeline and surface the next milestone here.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                @include('events.partials.search-panel', [
                    'searchId' => 'dashboard-shared-event-search',
                ])

                <div class="app-panel px-6 py-6" data-weather-widget>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <span class="section-label">Local Weather</span>
                            <h2 class="section-title mt-4 text-slate-950">Live conditions</h2>
                        </div>

                        <button type="button" class="btn-secondary !px-4 !py-2 !text-xs" data-weather-trigger>
                            Use My Location
                        </button>
                    </div>

                    <p class="status-copy mt-4 text-sm leading-7 text-slate-500" data-weather-status>
                        Use your device location to load current weather for day-of event planning.
                    </p>

                    <div class="mt-6 rounded-[24px] bg-slate-950 px-5 py-5 text-white">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-orange-300" data-weather-location>
                            Current device location
                        </p>

                        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end">
                            <p class="text-4xl font-semibold text-white sm:text-5xl" data-weather-temperature>--</p>

                            <div class="pb-1">
                                <p class="text-lg font-semibold text-white" data-weather-description>
                                    Waiting for location access
                                </p>
                                <p class="text-sm text-white/60" data-weather-summary>
                                    High -- · Low --
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="weather-metric">
                            <p class="weather-metric-label">Feels Like</p>
                            <p class="weather-metric-value" data-weather-feels-like>--</p>
                        </div>

                        <div class="weather-metric">
                            <p class="weather-metric-label">Precipitation</p>
                            <p class="weather-metric-value" data-weather-precipitation>--</p>
                        </div>

                        <div class="weather-metric">
                            <p class="weather-metric-label">Wind</p>
                            <p class="weather-metric-value" data-weather-wind>--</p>
                        </div>
                    </div>
                </div>

                <div class="app-panel px-6 py-6">
                    <span class="section-label">Completed</span>
                    <h2 class="section-title mt-4 text-slate-950">Recent wrap-ups</h2>

                    <div class="mt-6 space-y-4">
                        @forelse ($recentActivity as $event)
                            <div class="rounded-[22px] border border-slate-100 bg-slate-50 px-5 py-4">
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">
                                    {{ $event->event_date->format('d M Y') }}
                                </p>
                                <h3 class="card-title mt-2 text-slate-950">{{ $event->title }}</h3>
                                <p class="mt-2 text-sm text-slate-600">{{ $event->location }}</p>
                            </div>
                        @empty
                            <p class="text-sm leading-7 text-slate-600">
                                Completed events will appear here once your schedule starts building history.
                            </p>
                        @endforelse
                    </div>
                </div>

                <div class="app-panel px-6 py-6">
                    <span class="section-label">Quick Notes</span>
                    <h2 class="section-title mt-4 text-slate-950">How this dashboard behaves</h2>
                    <div class="mt-4 space-y-3 text-sm leading-7 text-slate-600">
                        <p>Events happening today stay in the upcoming count, so same-day work does not disappear from the active pipeline.</p>
                        <p>The shared event search lets you look up events created by any user without opening the database manually.</p>
                        <p>Your event routes are now scoped to your own records, which prevents accidental edits to another user’s schedule.</p>
                        <p>The weather panel uses your browser’s device location and Open-Meteo data when you choose to load it.</p>
                        <p>Use the event list for edits, deletes, and a full view of both upcoming and archived plans.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
