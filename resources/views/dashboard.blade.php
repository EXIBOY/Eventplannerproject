<x-app-layout>
    <div class="mx-auto max-w-6xl space-y-6">
        <section class="app-panel mesh-accent overflow-hidden px-7 py-8 sm:px-10 sm:py-10">
            <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
                <div>
                    <span class="section-label">Control Room</span>
                    <h1 class="mt-5 text-5xl leading-[0.96] text-slate-950 sm:text-6xl">
                        Keep your event calendar sharp and visible.
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">
                        Review the pipeline, spot the next milestone, and update your event schedule before details slip.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
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
                        <h2 class="mt-4 text-4xl text-white">{{ $nextEvent->title }}</h2>
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
                <p class="mt-4 text-5xl font-semibold text-slate-950">{{ $eventCount }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">Every active and completed event in your workspace.</p>
            </div>

            <div class="stat-card">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Upcoming</p>
                <p class="mt-4 text-5xl font-semibold text-slate-950">{{ $upcomingEvents }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">Includes events scheduled for today and all future dates.</p>
            </div>

            <div class="stat-card">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Recent Activity</p>
                <p class="mt-4 text-5xl font-semibold text-slate-950">{{ $recentActivity->count() }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">Your latest completed events, kept handy for quick reference.</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="app-panel px-7 py-8 sm:px-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <span class="section-label">Schedule</span>
                        <h2 class="mt-4 text-4xl text-slate-950">Upcoming timeline</h2>
                    </div>

                    <a href="{{ route('events.index') }}" class="btn-secondary">
                        Manage Events
                    </a>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($upcomingSchedule->take(4) as $event)
                        <div class="event-card">
                            <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                                <div class="flex items-start gap-4">
                                    <div class="rounded-[22px] bg-orange-100 px-4 py-3 text-center text-orange-700">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em]">{{ $event->event_date->format('M') }}</p>
                                        <p class="mt-1 text-3xl font-semibold text-orange-900">{{ $event->event_date->format('d') }}</p>
                                    </div>

                                    <div>
                                        <h3 class="text-2xl text-slate-950">{{ $event->title }}</h3>
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
                <div class="app-panel px-6 py-6">
                    <span class="section-label">Completed</span>
                    <h2 class="mt-4 text-3xl text-slate-950">Recent wrap-ups</h2>

                    <div class="mt-6 space-y-4">
                        @forelse ($recentActivity as $event)
                            <div class="rounded-[22px] border border-slate-100 bg-slate-50 px-5 py-4">
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">
                                    {{ $event->event_date->format('d M Y') }}
                                </p>
                                <h3 class="mt-2 text-2xl text-slate-950">{{ $event->title }}</h3>
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
                    <h2 class="mt-4 text-3xl text-slate-950">How this dashboard behaves</h2>
                    <div class="mt-4 space-y-3 text-sm leading-7 text-slate-600">
                        <p>Events happening today stay in the upcoming count, so same-day work does not disappear from the active pipeline.</p>
                        <p>Your event routes are now scoped to your own records, which prevents accidental edits to another user’s schedule.</p>
                        <p>Use the event list for edits, deletes, and a full view of both upcoming and archived plans.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
