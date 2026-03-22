<x-app-layout>
    <div class="mx-auto max-w-6xl space-y-6">
        @if (session('status'))
            <div class="ajax-feedback border-emerald-200 bg-emerald-50 text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <section class="app-panel mesh-accent px-5 py-6 sm:px-8 sm:py-8 lg:px-10 lg:py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-3xl">
                    <span class="section-label">{{ $event->categoryLabel() }}</span>
                    <h1 class="page-hero-title mt-5 text-slate-950">{{ $event->title }}</h1>
                    <p class="lede-copy mt-5 text-slate-600">
                        {{ $event->description ?: 'No description has been added for this event yet.' }}
                    </p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="event-tag">{{ $event->statusLabel() }}</span>
                        <span class="event-tag">{{ $event->visibilityLabel() }}</span>
                        @if ($event->capacity)
                            <span class="event-tag">{{ $event->capacity }} capacity</span>
                        @endif
                    </div>
                </div>

                <div class="action-row lg:justify-end">
                    <a href="{{ route('events.export', $event) }}" class="btn-secondary">
                        Export Calendar
                    </a>

                    @can('update', $event)
                        <a href="{{ route('events.edit', $event) }}" class="btn-primary">
                            Edit Event
                        </a>
                    @endcan
                </div>
            </div>
        </section>

        <section class="detail-grid">
            <div class="detail-card">
                <p class="detail-label">Date</p>
                <p class="detail-value">{{ $event->event_date?->format('l, d M Y') }}</p>
            </div>
            <div class="detail-card">
                <p class="detail-label">Time</p>
                <p class="detail-value">{{ $event->timeLabel() }}</p>
            </div>
            <div class="detail-card">
                <p class="detail-label">Location</p>
                <p class="detail-value">{{ $event->location }}</p>
            </div>
            <div class="detail-card">
                <p class="detail-label">Organizer</p>
                <p class="detail-value">{{ $event->organizer_name ?: $event->user->name }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $event->organizer_email ?: $event->user->email }}</p>
            </div>
            <div class="detail-card">
                <p class="detail-label">Visibility</p>
                <p class="detail-value">{{ $event->visibilityLabel() }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $event->visibilityDescription() }}</p>
            </div>
            <div class="detail-card">
                <p class="detail-label">Reminder</p>
                <p class="detail-value">
                    {{ $event->reminder_minutes ? (\App\Models\Event::reminderOptions()[$event->reminder_minutes] ?? $event->reminder_minutes.' minutes before') : 'No automatic reminder' }}
                </p>
                @if ($event->reminder_sent_at)
                    <p class="mt-1 text-sm text-slate-600">Last sent {{ $event->reminder_sent_at->diffForHumans() }}</p>
                @endif
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr] lg:items-start">
            <div class="app-panel px-6 py-6">
                <span class="section-label">Event Brief</span>
                <h2 class="section-title mt-4 text-slate-950">Operational notes</h2>
                <div class="mt-5 rounded-[22px] border border-slate-100 bg-slate-50 px-5 py-5 text-sm leading-7 text-slate-600">
                    {{ $event->description ?: 'No briefing notes have been added yet. Use the edit screen to add a run-of-show, production notes, or stakeholder context.' }}
                </div>

                <div class="action-row mt-6">
                    <a href="{{ route('events.index') }}" class="btn-secondary">
                        Back to Events
                    </a>

                    @can('sendReminder', $event)
                        <form method="POST" action="{{ route('events.send-reminder', $event) }}">
                            @csrf
                            <button type="submit" class="btn-secondary">
                                Send Reminder
                            </button>
                        </form>
                    @endcan

                    @can('delete', $event)
                        <form method="POST" action="{{ route('events.destroy', $event) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 transition hover:-translate-y-0.5 hover:bg-rose-100 sm:rounded-full sm:px-5">
                                Delete Event
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            <div class="space-y-6">
                <div class="app-panel px-6 py-6">
                    <span class="section-label">Owner</span>
                    <h2 class="section-title mt-4 text-slate-950">{{ $event->user->name }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $event->user->email }}</p>
                </div>

                <div class="app-panel px-6 py-6">
                    <span class="section-label">Related</span>
                    <h2 class="section-title mt-4 text-slate-950">Similar upcoming events</h2>

                    <div class="mt-6 space-y-4">
                        @forelse ($relatedEvents as $relatedEvent)
                            <div class="rounded-[22px] border border-slate-100 bg-slate-50 px-5 py-4">
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">
                                    {{ $relatedEvent->event_date?->format('d M Y') }} · {{ $relatedEvent->timeLabel() }}
                                </p>
                                <h3 class="card-title mt-2 text-slate-950">{{ $relatedEvent->title }}</h3>
                                <p class="mt-2 text-sm text-slate-600">{{ $relatedEvent->location }}</p>
                                <div class="mt-4">
                                    <a href="{{ route('events.show', $relatedEvent) }}" class="btn-secondary !px-4 !py-2 !text-xs">
                                        View Event
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm leading-7 text-slate-600">
                                Similar visible events will appear here once the schedule grows.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
