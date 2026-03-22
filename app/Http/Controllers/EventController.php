<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Support\EventPlanner\EventCalendarExporter;
use App\Support\EventPlanner\EventLibraryData;
use App\Support\EventPlanner\EventReminderService;
use App\Support\EventPlanner\EventSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    public function index(Request $request, EventLibraryData $eventLibraryData): View
    {
        $this->authorize('viewAny', Event::class);

        return view('events.index', $eventLibraryData->build($request->user()));
    }

    public function show(Request $request, Event $event): View
    {
        $this->authorize('view', $event);

        $relatedEvents = Event::query()
            ->visibleTo($request->user())
            ->where('id', '!=', $event->id)
            ->where('category', $event->category)
            ->upcoming()
            ->orderedChronologically()
            ->limit(3)
            ->get();

        return view('events.show', compact('event', 'relatedEvents'));
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);

        return view('events.create', [
            'event' => new Event([
                'status' => Event::STATUS_CONFIRMED,
                'category' => array_key_first(Event::categoryOptions()),
                'visibility' => Event::VISIBILITY_WORKSPACE,
                'reminder_minutes' => Event::DEFAULT_REMINDER_MINUTES,
            ]),
        ]);
    }

    public function search(Request $request, EventSearchService $eventSearchService): JsonResponse
    {
        $this->authorize('viewAny', Event::class);

        $filters = $eventSearchService->normalizeFilters($request->only([
            'q',
            'scope',
            'timeframe',
            'status',
            'category',
            'visibility',
            'sort',
        ]));

        $events = $eventSearchService
            ->search($request->user(), $filters)
            ->map(fn (Event $event): array => $this->serializeSearchResult($event, $request))
            ->values();

        return response()->json([
            'events' => $events,
            'meta' => [
                'count' => $events->count(),
                'query' => $filters['q'],
            ],
            'filters' => $filters,
        ]);
    }

    public function store(EventRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Event::class);

        $event = $request->user()->events()->create(
            $this->normalizeEventPayload($request->validated()),
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Event created successfully.',
                'redirect_url' => route('events.show', $event),
                'event' => $this->serializeEvent($event, $request),
            ], 201);
        }

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Event created successfully.');
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('events.edit', compact('event'));
    }

    public function update(EventRequest $request, Event $event): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $event);

        $payload = $this->normalizeEventPayload($request->validated());

        if ($this->scheduleChanged($event, $payload)) {
            $payload['reminder_sent_at'] = null;
        }

        $event->update($payload);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Event updated successfully.',
                'redirect_url' => route('events.show', $event),
                'event' => $this->serializeEvent($event->fresh(), $request),
            ]);
        }

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Event updated successfully.');
    }

    public function destroy(Request $request, Event $event): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $event);

        $eventId = $event->id;
        $event->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Event deleted successfully.',
                'event_id' => $eventId,
            ]);
        }

        return redirect()
            ->route('events.index')
            ->with('status', 'Event deleted successfully.');
    }

    public function export(Request $request, Event $event, EventCalendarExporter $eventCalendarExporter): Response
    {
        $this->authorize('view', $event);

        return response(
            $eventCalendarExporter->forEvent($event),
            200,
            [
                'Content-Type' => 'text/calendar; charset=UTF-8',
                'Content-Disposition' => sprintf('attachment; filename="%s.ics"', Str::slug($event->title)),
            ],
        );
    }

    public function calendar(Request $request, EventCalendarExporter $eventCalendarExporter): Response
    {
        $this->authorize('viewAny', Event::class);

        $events = Event::query()
            ->ownedBy($request->user())
            ->orderedChronologically()
            ->get();

        return response(
            $eventCalendarExporter->forCollection($events, sprintf('%s calendar', $request->user()->name)),
            200,
            [
                'Content-Type' => 'text/calendar; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="event-planner-calendar.ics"',
            ],
        );
    }

    public function sendReminder(
        Request $request,
        Event $event,
        EventReminderService $eventReminderService,
    ): RedirectResponse|JsonResponse {
        $this->authorize('sendReminder', $event);

        $sent = $eventReminderService->send($event, force: true);
        $message = $sent
            ? 'Reminder sent successfully.'
            : 'Reminder could not be sent for this event.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], $sent ? 200 : 422);
        }

        return redirect()
            ->route('events.show', $event)
            ->with('status', $message);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function scheduleChanged(Event $event, array $payload): bool
    {
        return Arr::only($payload, [
            'event_date',
            'start_time',
            'end_time',
            'status',
            'reminder_minutes',
        ]) !== Arr::only($event->getAttributes(), [
            'event_date',
            'start_time',
            'end_time',
            'status',
            'reminder_minutes',
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizeEventPayload(array $payload): array
    {
        foreach ([
            'description',
            'start_time',
            'end_time',
            'organizer_name',
            'organizer_email',
            'capacity',
            'reminder_minutes',
        ] as $field) {
            if (blank($payload[$field] ?? null)) {
                $payload[$field] = null;
            }
        }

        if (!empty($payload['organizer_email'])) {
            $payload['organizer_email'] = strtolower((string) $payload['organizer_email']);
        }

        return $payload;
    }

    /**
     * @return array<string, bool|int|string|null>
     */
    private function serializeEvent(Event $event, Request $request): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'event_date' => $event->event_date?->toDateString(),
            'event_date_label' => $event->event_date?->format('l, d M Y'),
            'start_time' => $event->start_time ? substr($event->start_time, 0, 5) : null,
            'end_time' => $event->end_time ? substr($event->end_time, 0, 5) : null,
            'time_label' => $event->timeLabel(),
            'status' => $event->status,
            'status_label' => $event->statusLabel(),
            'category' => $event->category,
            'category_label' => $event->categoryLabel(),
            'location' => $event->location,
            'organizer_name' => $event->organizer_name,
            'organizer_email' => $event->organizer_email,
            'capacity' => $event->capacity,
            'visibility' => $event->visibility,
            'visibility_label' => $event->visibilityLabel(),
            'reminder_minutes' => $event->reminder_minutes,
            'show_url' => route('events.show', $event),
            'edit_url' => $request->user()?->can('update', $event) ? route('events.edit', $event) : null,
            'calendar_url' => route('events.export', $event),
        ];
    }

    /**
     * @return array<string, bool|int|string|null>
     */
    private function serializeSearchResult(Event $event, Request $request): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'description_excerpt' => Str::limit($event->description ?: 'No description added yet.', 160),
            'event_date' => $event->event_date?->toDateString(),
            'event_date_label' => $event->event_date?->format('l, d M Y'),
            'time_label' => $event->timeLabel(),
            'location' => $event->location,
            'status' => $event->status,
            'status_label' => $event->statusLabel(),
            'category' => $event->category,
            'category_label' => $event->categoryLabel(),
            'visibility' => $event->visibility,
            'visibility_label' => $event->visibilityLabel(),
            'owner_name' => $event->user?->name,
            'owner_email' => $event->user?->email,
            'is_owner' => $request->user()?->id === $event->user_id,
            'view_url' => route('events.show', $event),
            'edit_url' => $request->user()?->can('update', $event) ? route('events.edit', $event) : null,
            'calendar_url' => route('events.export', $event),
        ];
    }
}
