<div class="app-panel px-6 py-6" data-event-search data-search-url="{{ route('events.search') }}">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <span class="section-label">Explore Events</span>
            <h2 class="section-title mt-4 text-slate-950">Search every stored event</h2>
        </div>

        <span class="event-tag" data-search-count>0 matches</span>
    </div>

    <label for="{{ $searchId }}" class="mt-5 block text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">
        Search by title, location, or organizer
    </label>
    <input
        id="{{ $searchId }}"
        type="search"
        class="form-input"
        placeholder="Try Spring Brand Launch, London, or planner@example.com"
        autocomplete="off"
        data-search-input
    >

    <p class="status-copy mt-3 text-sm leading-7 text-slate-500" data-search-status>
        Type at least 2 characters to search events created by any user in the database.
    </p>

    <div class="mt-5 space-y-3" data-search-results></div>

    <div class="event-card mt-5 hidden text-center" data-search-empty>
        <h3 class="section-title text-slate-950">No matching events yet</h3>
        <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-600">
            Try a broader search by event title, venue, city, or the creator's name or email.
        </p>
    </div>
</div>
