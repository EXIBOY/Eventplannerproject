<x-app-layout>
    <div class="mx-auto max-w-5xl">
        <section class="grid gap-6 lg:grid-cols-[1fr_0.36fr]">
            <div class="app-panel px-7 py-8 sm:px-10 sm:py-10">
                <span class="section-label">Edit Event</span>
                <h1 class="page-hero-title mt-5 text-slate-950">Refine the event brief.</h1>
                <p class="lede-copy mt-5 max-w-2xl text-slate-600">
                    Update the schedule, tighten the description, or correct venue details without affecting the rest of your timeline.
                </p>

                @include('events.partials.form', [
                    'action' => route('events.update', $event),
                    'method' => 'PUT',
                    'formMode' => 'edit',
                    'submitLabel' => 'Update Event',
                ])
            </div>

            <aside class="space-y-4">
                <div class="app-panel px-6 py-6">
                    <span class="section-label">Current Status</span>
                    <p class="section-title mt-4 text-slate-950">{{ $event->event_date->format('d M Y') }}</p>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $event->timeLabel() }} · {{ $event->location }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="event-tag">{{ $event->statusLabel() }}</span>
                        <span class="event-tag">{{ $event->categoryLabel() }}</span>
                        <span class="event-tag">{{ $event->visibilityLabel() }}</span>
                    </div>
                </div>

                <div class="app-panel px-6 py-6">
                    <span class="section-label">Reminder</span>
                    <div class="mt-4 space-y-3 text-sm leading-7 text-slate-600">
                        <p>Keep titles short and specific so the dashboard remains easy to scan.</p>
                        <p>If the schedule changes, the reminder clock resets so the event can notify again at the new time.</p>
                    </div>
                </div>
            </aside>
        </section>
    </div>
</x-app-layout>
