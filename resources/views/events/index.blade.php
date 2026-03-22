<x-app-layout>
    <div class="mx-auto max-w-6xl space-y-6">
        <section class="app-panel mesh-accent px-5 py-6 sm:px-8 sm:py-8 lg:px-10 lg:py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="section-label">Event Library</span>
                    <h1 class="page-hero-title mt-5 text-slate-950">Your event pipeline, organized.</h1>
                    <p class="lede-copy mt-5 max-w-2xl text-slate-600">
                        Review what is coming up, revisit completed work, and keep each event brief current from one page.
                    </p>
                </div>

                <div class="action-row">
                    <a href="{{ route('events.create') }}" class="btn-primary">
                        Create Event
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn-secondary">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div class="rounded-[24px] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800 shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <section class="grid gap-4 md:grid-cols-3">
            <div class="stat-card">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Upcoming</p>
                <p class="metric-number mt-4 text-slate-950">{{ $upcomingEvents->count() }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">Everything still ahead, including events happening today.</p>
            </div>
            <div class="stat-card">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Archived</p>
                <p class="metric-number mt-4 text-slate-950">{{ $pastEvents->count() }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">Past events kept available for notes, references, and revisions.</p>
            </div>
            <div class="stat-card">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Focus</p>
                <p class="section-title mt-4 text-slate-950">
                    {{ $upcomingEvents->first()?->title ?? 'Create your next event' }}
                </p>
                <p class="mt-3 text-sm leading-6 text-slate-600">The first card below is your next active brief.</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.18fr_0.82fr]">
            <div class="app-panel px-5 py-6 sm:px-8 sm:py-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="section-label">Upcoming</span>
                        <h2 class="page-title mt-4 text-slate-950">Active events</h2>
                    </div>
                    <span class="event-tag">{{ $upcomingEvents->count() }} scheduled</span>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($upcomingEvents as $event)
                        <div class="event-card">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                    <div class="shrink-0 self-start rounded-[22px] bg-orange-100 px-4 py-3 text-center text-orange-800">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em]">{{ $event->event_date->format('M') }}</p>
                                        <p class="mt-1 text-3xl font-semibold">{{ $event->event_date->format('d') }}</p>
                                    </div>

                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="section-title text-slate-950">{{ $event->title }}</h3>
                                            @if ($event->event_date->isToday())
                                                <span class="event-tag">Today</span>
                                            @endif
                                        </div>

                                        <p class="mt-2 text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">
                                            {{ $event->event_date->format('l, d M Y') }} · {{ $event->location }}
                                        </p>

                                        <p class="mt-4 text-sm leading-7 text-slate-600">
                                            {{ $event->description ?: 'No description added yet. Use edit to capture the planning brief for this event.' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="action-row lg:justify-end">
                                    <a href="{{ route('events.edit', $event) }}" class="btn-secondary">
                                        Edit
                                    </a>

                                    <form action="{{ route('events.destroy', $event) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 transition hover:-translate-y-0.5 hover:bg-rose-100 sm:w-auto sm:rounded-full sm:px-5">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="event-card text-center">
                            <h3 class="section-title text-slate-950">No active events yet</h3>
                            <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-600">
                                Add your first brief to start building an upcoming schedule.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('events.create') }}" class="btn-primary">
                                    Create Your First Event
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <div class="app-panel px-6 py-6">
                    <span class="section-label">Archive</span>
                    <h2 class="section-title mt-4 text-slate-950">Completed events</h2>

                    <div class="mt-6 space-y-4">
                        @forelse ($pastEvents as $event)
                            <div class="rounded-[22px] border border-slate-100 bg-slate-50 px-5 py-4">
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">
                                    {{ $event->event_date->format('d M Y') }}
                                </p>
                                <h3 class="card-title mt-2 text-slate-950">{{ $event->title }}</h3>
                                <p class="mt-2 text-sm text-slate-600">{{ $event->location }}</p>
                                @if ($event->description)
                                    <p class="mt-3 text-sm leading-7 text-slate-600">
                                        {{ \Illuminate\Support\Str::limit($event->description, 120) }}
                                    </p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm leading-7 text-slate-600">
                                Finished events will appear here automatically as their dates move into the past.
                            </p>
                        @endforelse
                    </div>
                </div>

                <div class="app-panel px-6 py-6">
                    <span class="section-label">Workflow</span>
                    <h2 class="section-title mt-4 text-slate-950">What changed</h2>
                    <div class="mt-4 space-y-3 text-sm leading-7 text-slate-600">
                        <p>Event lists are sorted by date so the next milestone stays on top.</p>
                        <p>Validation now protects empty titles, locations, and malformed dates.</p>
                        <p>Each edit and delete action is scoped to the signed-in user’s own events.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
