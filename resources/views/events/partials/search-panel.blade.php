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
        name="q"
        class="form-input"
        placeholder="Try Spring Brand Launch, London, or planner@example.com"
        autocomplete="off"
        data-search-field
    >

    <div class="search-filter-grid">
        <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">
            Scope
            <select name="scope" class="form-input !mt-2" data-search-field>
                <option value="all">All visible events</option>
                <option value="mine">Only my events</option>
                <option value="shared">Only shared events</option>
            </select>
        </label>

        <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">
            Timeframe
            <select name="timeframe" class="form-input !mt-2" data-search-field>
                <option value="all">All dates</option>
                <option value="upcoming">Upcoming</option>
                <option value="past">Past</option>
            </select>
        </label>

        <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">
            Status
            <select name="status" class="form-input !mt-2" data-search-field>
                <option value="any">Any status</option>
                @foreach (\App\Models\Event::statusOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">
            Category
            <select name="category" class="form-input !mt-2" data-search-field>
                <option value="any">Any category</option>
                @foreach (\App\Models\Event::categoryOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">
            Visibility
            <select name="visibility" class="form-input !mt-2" data-search-field>
                <option value="any">Any visibility</option>
                @foreach (\App\Models\Event::visibilityOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">
            Sort by
            <select name="sort" class="form-input !mt-2" data-search-field>
                <option value="soonest">Soonest first</option>
                <option value="latest">Latest first</option>
                <option value="title">Title</option>
            </select>
        </label>
    </div>

    <p class="status-copy mt-3 text-sm leading-7 text-slate-500" data-search-status>
        Search by title, location, organizer, category, status, or visibility to find events stored in the database.
    </p>

    <div class="mt-5 space-y-3" data-search-results></div>

    <div class="event-card mt-5 hidden text-center" data-search-empty>
        <h3 class="section-title text-slate-950">No matching events yet</h3>
        <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-600">
            Try a broader search by event title, venue, city, or the creator's name or email.
        </p>
    </div>
</div>
