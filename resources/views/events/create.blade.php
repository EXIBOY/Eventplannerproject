<x-app-layout>
    <div class="mx-auto max-w-5xl">
        <section class="grid gap-6 lg:grid-cols-[1fr_0.36fr]">
            <div class="app-panel px-7 py-8 sm:px-10 sm:py-10">
                <span class="section-label">Create Event</span>
                <h1 class="page-hero-title mt-5 text-slate-950">Capture the next brief.</h1>
                <p class="lede-copy mt-5 max-w-2xl text-slate-600">
                    Add the essentials now. You can refine the description and schedule details later without losing the timeline.
                </p>

                @include('events.partials.form', [
                    'action' => route('events.store'),
                    'method' => 'POST',
                    'submitLabel' => 'Save Event',
                ])
            </div>

            <aside class="space-y-4">
                <div class="app-panel px-6 py-6">
                    <span class="section-label">Checklist</span>
                    <div class="mt-4 space-y-3 text-sm leading-7 text-slate-600">
                        <p>Use a clear event title people will recognize instantly.</p>
                        <p>Add the city, venue, or workspace in the location field.</p>
                        <p>Descriptions work best as short operational briefs, not marketing copy.</p>
                    </div>
                </div>

                <div class="app-panel px-6 py-6">
                    <span class="section-label">Tip</span>
                    <p class="section-title mt-4 text-slate-950">Start simple.</p>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Once the event exists, it will immediately appear on your dashboard and event list for follow-up.
                    </p>
                </div>
            </aside>
        </section>
    </div>
</x-app-layout>
