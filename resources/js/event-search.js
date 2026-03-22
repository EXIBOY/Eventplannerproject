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
                            ${escapeHtml(event.event_date_label ?? 'Date unavailable')} · ${escapeHtml(event.location ?? 'Location unavailable')}
                        </p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">${escapeHtml(event.description_excerpt ?? '')}</p>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
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
        const input = query(widget, '[data-search-input]');
        const status = query(widget, '[data-search-status]');
        const count = query(widget, '[data-search-count]');
        const searchUrl = widget.dataset.searchUrl;

        if (!input || !status || !count || !searchUrl) {
            return;
        }

        let debounceId = null;
        let controller = null;
        let currentQuery = '';

        const updateState = (message, countValue = 0) => {
            status.textContent = message;
            count.textContent = pluralizeResults(countValue);
        };

        updateState('Type at least 2 characters to search events created by any user in the database.', 0);

        input.addEventListener('input', () => {
            const term = input.value.trim();

            window.clearTimeout(debounceId);

            if (controller) {
                controller.abort();
                controller = null;
            }

            if (term === '') {
                currentQuery = '';
                renderSearchResults(widget, []);
                query(widget, '[data-search-empty]')?.classList.add('hidden');
                updateState('Type at least 2 characters to search events created by any user in the database.', 0);
                return;
            }

            if (term.length < 2) {
                currentQuery = term;
                renderSearchResults(widget, []);
                updateState('Keep typing. Search starts after 2 characters.', 0);
                return;
            }

            debounceId = window.setTimeout(async () => {
                currentQuery = term;
                controller = new AbortController();
                updateState(`Searching for "${term}"...`, 0);

                try {
                    const url = new URL(searchUrl, window.location.origin);
                    url.searchParams.set('q', term);

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

                    if (currentQuery !== term) {
                        return;
                    }

                    const events = Array.isArray(payload.events) ? payload.events : [];

                    renderSearchResults(widget, events);
                    updateState(
                        events.length === 0
                            ? `No stored events matched "${term}".`
                            : `Showing matches for "${term}".`,
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
        });
    });
}

ready(() => {
    initEventSearch();
});
