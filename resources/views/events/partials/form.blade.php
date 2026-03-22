<form method="POST" action="{{ $action }}" class="mt-8 space-y-6" data-event-form data-mode="{{ $formMode }}">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="ajax-feedback hidden" data-form-feedback aria-live="polite"></div>

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
                data-error-input
                required
            >
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="title"></p>
        </div>

        <div class="md:col-span-2">
            <label for="description" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Description</label>
            <textarea
                id="description"
                name="description"
                rows="5"
                class="form-input"
                placeholder="Add the brief, run-of-show context, or any planning notes that should travel with this event."
                data-error-input
            >{{ old('description', $event->description) }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="description"></p>
        </div>

        <div>
            <label for="event_date" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Date</label>
            <input
                id="event_date"
                type="date"
                name="event_date"
                value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}"
                class="form-input"
                data-error-input
                required
            >
            <x-input-error :messages="$errors->get('event_date')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="event_date"></p>
        </div>

        <div>
            <label for="start_time" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Start Time</label>
            <input
                id="start_time"
                type="time"
                name="start_time"
                value="{{ old('start_time', $event->start_time ? substr($event->start_time, 0, 5) : '') }}"
                class="form-input"
                data-error-input
            >
            <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="start_time"></p>
        </div>

        <div>
            <label for="end_time" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">End Time</label>
            <input
                id="end_time"
                type="time"
                name="end_time"
                value="{{ old('end_time', $event->end_time ? substr($event->end_time, 0, 5) : '') }}"
                class="form-input"
                data-error-input
            >
            <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="end_time"></p>
        </div>

        <div>
            <label for="category" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Category</label>
            <select id="category" name="category" class="form-input" data-error-input required>
                @foreach (\App\Models\Event::categoryOptions() as $value => $label)
                    <option value="{{ $value }}" @selected(old('category', $event->category) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('category')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="category"></p>
        </div>

        <div>
            <label for="status" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Status</label>
            <select id="status" name="status" class="form-input" data-error-input required>
                @foreach (\App\Models\Event::statusOptions() as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $event->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="status"></p>
        </div>

        <div class="md:col-span-2" data-device-location>
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
                data-error-input
                required
            >
            <p class="status-copy mt-2 text-xs leading-6 text-slate-500" data-location-status>
                Browser location can fill this with your current street address if the venue is where you are right now.
            </p>
            <p class="mt-1 text-[11px] leading-6 text-slate-400">
                Address lookup uses OpenStreetMap Nominatim data.
            </p>
            <x-input-error :messages="$errors->get('location')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="location"></p>
        </div>

        <div>
            <label for="visibility" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Visibility</label>
            <select id="visibility" name="visibility" class="form-input" data-error-input required>
                @foreach (\App\Models\Event::visibilityOptions() as $value => $label)
                    <option value="{{ $value }}" @selected(old('visibility', $event->visibility) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="mt-2 text-xs leading-6 text-slate-500">
                Private stays yours, workspace is visible to signed-in users, and public can be shared by link.
            </p>
            <x-input-error :messages="$errors->get('visibility')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="visibility"></p>
        </div>

        <div>
            <label for="capacity" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Capacity</label>
            <input
                id="capacity"
                type="number"
                min="1"
                name="capacity"
                value="{{ old('capacity', $event->capacity) }}"
                class="form-input"
                placeholder="80"
                data-error-input
            >
            <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="capacity"></p>
        </div>

        <div>
            <label for="organizer_name" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Organizer Name</label>
            <input
                id="organizer_name"
                type="text"
                name="organizer_name"
                value="{{ old('organizer_name', $event->organizer_name) }}"
                class="form-input"
                placeholder="Aisha Morgan"
                data-error-input
            >
            <x-input-error :messages="$errors->get('organizer_name')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="organizer_name"></p>
        </div>

        <div>
            <label for="organizer_email" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Organizer Email</label>
            <input
                id="organizer_email"
                type="email"
                name="organizer_email"
                value="{{ old('organizer_email', $event->organizer_email) }}"
                class="form-input"
                placeholder="events@example.com"
                data-error-input
            >
            <x-input-error :messages="$errors->get('organizer_email')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="organizer_email"></p>
        </div>

        <div class="md:col-span-2">
            <label for="reminder_minutes" class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Reminder</label>
            <select id="reminder_minutes" name="reminder_minutes" class="form-input" data-error-input>
                <option value="">No automatic reminder</option>
                @foreach (\App\Models\Event::reminderOptions() as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('reminder_minutes', $event->reminder_minutes) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="mt-2 text-xs leading-6 text-slate-500">
                Manual reminders can also be sent from the event details page.
            </p>
            <x-input-error :messages="$errors->get('reminder_minutes')" class="mt-2" />
            <p class="ajax-error hidden" data-ajax-error="reminder_minutes"></p>
        </div>
    </div>

    <div class="soft-rule"></div>

    <div class="action-row">
        <button type="submit" class="btn-primary" data-form-submit data-idle-label="{{ $submitLabel }}" data-pending-label="{{ $formMode === 'edit' ? 'Updating...' : 'Saving...' }}">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('events.index') }}" class="btn-secondary">
            Cancel
        </a>
        <a href="{{ route('events.index') }}" class="btn-secondary hidden" data-form-success-link>
            Open Saved Event
        </a>
    </div>
</form>
