const FEEDBACK_TONES = {
    success: ['border-emerald-200', 'bg-emerald-50', 'text-emerald-800'],
    error: ['border-rose-200', 'bg-rose-50', 'text-rose-800'],
    pending: ['border-amber-200', 'bg-amber-50', 'text-amber-800'],
};

function onReady(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }

    callback();
}

function qs(root, selector) {
    return root.querySelector(selector);
}

function qsa(root, selector) {
    return Array.from(root.querySelectorAll(selector));
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function setFeedbackState(element, message, tone) {
    if (!element) {
        return;
    }

    Object.values(FEEDBACK_TONES).flat().forEach((className) => {
        element.classList.remove(className);
    });

    if (!message) {
        element.classList.add('hidden');
        element.textContent = '';
        return;
    }

    element.classList.remove('hidden');
    element.classList.add(...(FEEDBACK_TONES[tone] ?? FEEDBACK_TONES.success));
    element.textContent = message;
}

function clearFieldErrors(form) {
    qsa(form, '[data-ajax-error]').forEach((element) => {
        element.textContent = '';
        element.classList.add('hidden');
    });

    qsa(form, '[data-error-input]').forEach((element) => {
        element.classList.remove('border-rose-300', 'focus:border-rose-400', 'focus:ring-rose-300');
    });
}

function applyFieldErrors(form, errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const errorElement = qs(form, `[data-ajax-error="${field}"]`);
        const input = qs(form, `[name="${field}"]`);

        if (errorElement) {
            errorElement.textContent = Array.isArray(messages) ? messages.join(' ') : String(messages);
            errorElement.classList.remove('hidden');
        }

        if (input) {
            input.classList.add('border-rose-300', 'focus:border-rose-400', 'focus:ring-rose-300');
        }
    });
}

async function parseJson(response) {
    const contentType = response.headers.get('content-type') ?? '';

    if (!contentType.includes('application/json')) {
        return null;
    }

    return response.json();
}

function resetCreateForm(form) {
    form.reset();

    const locationStatus = qs(form, '[data-location-status]');

    if (locationStatus) {
        locationStatus.textContent = 'Browser location can fill this with your current street address if the venue is where you are right now.';
        locationStatus.classList.remove('text-amber-700', 'text-emerald-700', 'text-rose-700');
        locationStatus.classList.add('text-slate-500');
    }
}

function syncFormWithEvent(form, event) {
    const fieldMap = {
        title: event.title ?? '',
        description: event.description ?? '',
        event_date: event.event_date ?? '',
        start_time: event.start_time ?? '',
        end_time: event.end_time ?? '',
        status: event.status ?? '',
        category: event.category ?? '',
        location: event.location ?? '',
        organizer_name: event.organizer_name ?? '',
        organizer_email: event.organizer_email ?? '',
        capacity: event.capacity ?? '',
        visibility: event.visibility ?? '',
        reminder_minutes: event.reminder_minutes ?? '',
    };

    Object.entries(fieldMap).forEach(([field, value]) => {
        const input = qs(form, `[name="${field}"]`);

        if (input) {
            input.value = value;
        }
    });
}

function initAjaxEventForms() {
    qsa(document, '[data-event-form]').forEach((form) => {
        const submitButton = qs(form, '[data-form-submit]');
        const feedback = qs(form, '[data-form-feedback]');
        const successLink = qs(form, '[data-form-success-link]');
        const mode = form.dataset.mode ?? 'create';
        const idleLabel = submitButton?.dataset.idleLabel ?? submitButton?.textContent?.trim() ?? 'Save';
        const pendingLabel = submitButton?.dataset.pendingLabel ?? 'Saving...';

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            clearFieldErrors(form);
            setFeedbackState(feedback, mode === 'edit' ? 'Updating event...' : 'Saving event...', 'pending');

            if (successLink) {
                successLink.classList.add('hidden');
            }

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = pendingLabel;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: new FormData(form),
                });

                const payload = await parseJson(response);

                if (response.status === 422 && payload?.errors) {
                    applyFieldErrors(form, payload.errors);
                    setFeedbackState(feedback, 'Please correct the highlighted fields and try again.', 'error');
                    return;
                }

                if (!response.ok || !payload) {
                    throw new Error('The event request could not be completed.');
                }

                if (mode === 'create') {
                    resetCreateForm(form);
                } else if (payload.event) {
                    syncFormWithEvent(form, payload.event);
                }

                setFeedbackState(feedback, payload.message ?? 'Event saved successfully.', 'success');

                if (successLink && payload.redirect_url) {
                    successLink.href = payload.redirect_url;
                    successLink.classList.remove('hidden');
                }
            } catch (error) {
                setFeedbackState(
                    feedback,
                    error instanceof Error ? error.message : 'The event request could not be completed.',
                    'error',
                );
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = idleLabel;
                }
            }
        });
    });
}

function updateUpcomingSummary(library) {
    const cards = qsa(library, '[data-event-card]');
    const count = cards.length;
    const focusTitle = cards[0]?.dataset.eventTitle ?? 'Create your next event';

    qsa(library, '[data-upcoming-count]').forEach((element) => {
        element.textContent = String(count);
    });

    const scheduledLabel = qs(library, '[data-scheduled-count]');

    if (scheduledLabel) {
        scheduledLabel.textContent = `${count} scheduled`;
    }

    const focusElement = qs(library, '[data-focus-title]');

    if (focusElement) {
        focusElement.textContent = focusTitle;
    }

    const emptyState = qs(library, '[data-upcoming-empty]');

    if (emptyState) {
        emptyState.classList.toggle('hidden', count !== 0);
    }
}

function initAjaxEventDeletes() {
    qsa(document, '[data-event-library]').forEach((library) => {
        const feedback = qs(library, '[data-event-action-feedback]');

        qsa(library, '[data-event-delete-form]').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = qs(form, '[data-delete-submit]');
                const card = form.closest('[data-event-card]');

                setFeedbackState(feedback, 'Deleting event...', 'pending');

                if (button) {
                    button.disabled = true;
                    button.textContent = 'Deleting...';
                }

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': getCsrfToken(),
                        },
                        body: new FormData(form),
                    });

                    const payload = await parseJson(response);

                    if (!response.ok || !payload) {
                        throw new Error('The event could not be deleted.');
                    }

                    card?.remove();
                    updateUpcomingSummary(library);
                    setFeedbackState(feedback, payload.message ?? 'Event deleted successfully.', 'success');
                } catch (error) {
                    setFeedbackState(
                        feedback,
                        error instanceof Error ? error.message : 'The event could not be deleted.',
                        'error',
                    );
                } finally {
                    if (button) {
                        button.disabled = false;
                        button.textContent = 'Delete';
                    }
                }
            });
        });
    });
}

onReady(() => {
    initAjaxEventForms();
    initAjaxEventDeletes();
});
