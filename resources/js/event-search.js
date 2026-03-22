function ready(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }

    callback();
}

function query(root, selector) {
    return root.querySelector(selector);
}

function queryAll(root, selector) {
    return Array.from(root.querySelectorAll(selector));
}

function pluralizeResults(count) {
    return `${count} ${count === 1 ? 'match' : 'matches'}`;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function getFilters(widget) {
    return queryAll(widget, '[data-search-field]').reduce((filters, field) => {
        filters[field.name] = field.value.trim();

        return filters;
    }, {});
}

function hasSearchIntent(filters) {
    return Object.entries(filters).some(([key, value]) => {
        if (key === 'sort') {
            return false;
        }

        return value !== '' && value !== 'all' && value !== 'any';
    }) || (filters.q ?? '') !== '';
}

function renderSearchResults(widget, events) {
    const results = query(widget, '[data-search-results]');
    const emptyState = query(widget, '[data-search-empty]');

    if (!results || !emptyState) {
        return;
    }

    if (events.length === 0) {
        results.innerHTML = '';
        emptyState.classList.remove('hidden');
        return;
    }

    emptyState.classList.add('hidden');
    results.innerHTML = events.map((event) => {
        const ownerLabel = event.owner_email
            ? `${event.owner_name} · ${event.owner_email}`
            : (event.owner_name ?? 'Unknown organizer');
        const action = event.edit_url
            ? `<a href="${escapeHtml(event.edit_url)}" class="btn-secondary !px-4 !py-2 !text-xs">Edit</a>`
            : '<span class="event-tag">Shared result</span>';

        return `
            <article class="search-result-card">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="search-result-meta">Created by ${escapeHtml(ownerLabel)}</p>
                        <h3 class="card-title mt-2 text-slate-950">${escapeHtml(event.title)}</h3>
                        <p class="mt-2 text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">
                            ${escapeHtml(event.event_date_label ?? 'Date unavailable')} · ${escapeHtml(event.time_label ?? 'All day')} · ${escapeHtml(event.location ?? 'Location unavailable')}
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="event-tag">${escapeHtml(event.status_label ?? 'Status')}</span>
                            <span class="event-tag">${escapeHtml(event.category_label ?? 'Category')}</span>
                            <span class="event-tag">${escapeHtml(event.visibility_label ?? 'Visibility')}</span>
                        </div>
                        <p class="mt-3 text-sm leading-7 text-slate-600">${escapeHtml(event.description_excerpt ?? '')}</p>
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <a href="${escapeHtml(event.view_url)}" class="btn-secondary !px-4 !py-2 !text-xs">View</a>
                        <a href="${escapeHtml(event.calendar_url)}" class="btn-secondary !px-4 !py-2 !text-xs">Calendar</a>
                        ${event.is_owner ? '<span class="event-tag">Your event</span>' : ''}
                        ${action}
                    </div>
                </div>
            </article>
        `;
    }).join('');
}

function initEventSearch() {
    document.querySelectorAll('[data-event-search]').forEach((widget) => {
        const status = query(widget, '[data-search-status]');
        const count = query(widget, '[data-search-count]');
        const searchUrl = widget.dataset.searchUrl;
        const fields = queryAll(widget, '[data-search-field]');

        if (!status || !count || !searchUrl || fields.length === 0) {
            return;
        }

        let debounceId = null;
        let controller = null;
        let currentQuery = '';

        const updateState = (message, countValue = 0) => {
            status.textContent = message;
            count.textContent = pluralizeResults(countValue);
        };

        const runSearch = () => {
            const filters = getFilters(widget);

            window.clearTimeout(debounceId);

            if (controller) {
                controller.abort();
                controller = null;
            }

            if (!hasSearchIntent(filters)) {
                currentQuery = '';
                renderSearchResults(widget, []);
                query(widget, '[data-search-empty]')?.classList.add('hidden');
                updateState('Search by title, location, organizer, category, status, or visibility to find events stored in the database.', 0);
                return;
            }

            debounceId = window.setTimeout(async () => {
                currentQuery = JSON.stringify(filters);
                controller = new AbortController();
                updateState('Searching stored events...', 0);

                try {
                    const url = new URL(searchUrl, window.location.origin);

                    Object.entries(filters).forEach(([key, value]) => {
                        if (value !== '') {
                            url.searchParams.set(key, value);
                        }
                    });

                    const response = await fetch(url.toString(), {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        signal: controller.signal,
                    });

                    if (!response.ok) {
                        throw new Error('Event search could not be completed.');
                    }

                    const payload = await response.json();

                    if (currentQuery !== JSON.stringify(filters)) {
                        return;
                    }

                    const events = Array.isArray(payload.events) ? payload.events : [];

                    renderSearchResults(widget, events);
                    updateState(
                        events.length === 0 ? 'No stored events matched those filters.' : 'Showing matching events.',
                        events.length,
                    );
                } catch (error) {
                    if (error instanceof DOMException && error.name === 'AbortError') {
                        return;
                    }

                    renderSearchResults(widget, []);
                    query(widget, '[data-search-empty]')?.classList.add('hidden');
                    updateState(
                        error instanceof Error ? error.message : 'Event search could not be completed.',
                        0,
                    );
                } finally {
                    controller = null;
                }
            }, 250);
        };

        fields.forEach((field) => {
            field.addEventListener(field.tagName === 'SELECT' ? 'change' : 'input', runSearch);
        });

        updateState('Search by title, location, organizer, category, status, or visibility to find events stored in the database.', 0);
    });
}

ready(() => {
    initEventSearch();
});
