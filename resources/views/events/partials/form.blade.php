<form method="POST" action="{{ $action }}" class="mt-8 space-y-6">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="title" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Event Title</label>
            <input
                id="title"
                type="text"
                name="title"
                value="{{ old('title', $event->title) }}"
                class="form-input"
                placeholder="Summer Client Dinner"
                required
            >
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <label for="description" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Description</label>
            <textarea
                id="description"
                name="description"
                rows="5"
                class="form-input"
                placeholder="Add the brief, run-of-show context, or any planning notes that should travel with this event."
            >{{ old('description', $event->description) }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div>
            <label for="event_date" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Date</label>
            <input
                id="event_date"
                type="date"
                name="event_date"
                value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}"
                class="form-input"
                required
            >
            <x-input-error :messages="$errors->get('event_date')" class="mt-2" />
        </div>

        <div data-device-location>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <label for="location" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Location</label>
                <button type="button" class="btn-secondary !px-4 !py-2 !text-xs" data-location-trigger>
                    Use My Location
                </button>
            </div>
            <input
                id="location"
                type="text"
                name="location"
                value="{{ old('location', $event->location) }}"
                class="form-input"
                placeholder="Sea Containers, London"
                data-location-input
                required
            >
            <p class="status-copy mt-2 text-xs leading-6 text-slate-500" data-location-status>
                Browser location can fill this with your current street address if the venue is where you are right now.
            </p>
            <p class="mt-1 text-[11px] leading-6 text-slate-400">
                Address lookup uses OpenStreetMap Nominatim data.
            </p>
            <x-input-error :messages="$errors->get('location')" class="mt-2" />
        </div>
    </div>

    <div class="soft-rule"></div>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="btn-primary">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('events.index') }}" class="btn-secondary">
            Cancel
        </a>
    </div>
</form>
