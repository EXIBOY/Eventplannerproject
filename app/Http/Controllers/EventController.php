<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Auth::user()
            ->events()
            ->orderBy('event_date')
            ->orderBy('title')
            ->get();

        $today = today();
        $upcomingEvents = $events
            ->filter(fn (Event $event) => $event->event_date->greaterThanOrEqualTo($today))
            ->values();

        $pastEvents = $events
            ->filter(fn (Event $event) => $event->event_date->lt($today))
            ->sortByDesc('event_date')
            ->values();

        return view('events.index', compact('upcomingEvents', 'pastEvents'));
    }

    public function create(): View
    {
        return view('events.create', [
            'event' => new Event(),
        ]);
    }

    public function store(EventRequest $request): RedirectResponse|JsonResponse
    {
        $event = $request->user()->events()->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Event created successfully.',
                'redirect_url' => route('events.index'),
                'event' => $this->serializeEvent($event),
            ], 201);
        }

        return redirect()
            ->route('events.index')
            ->with('status', 'Event created successfully.');
    }

    public function edit(Event $event): View
    {
        $this->ensureOwnsEvent($event);

        return view('events.edit', compact('event'));
    }

    public function update(EventRequest $request, Event $event): RedirectResponse|JsonResponse
    {
        $this->ensureOwnsEvent($event);
        $event->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Event updated successfully.',
                'redirect_url' => route('events.index'),
                'event' => $this->serializeEvent($event->fresh()),
            ]);
        }

        return redirect()
            ->route('events.index')
            ->with('status', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse|JsonResponse
    {
        $this->ensureOwnsEvent($event);
        $eventId = $event->id;
        $event->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Event deleted successfully.',
                'event_id' => $eventId,
            ]);
        }

        return redirect()
            ->route('events.index')
            ->with('status', 'Event deleted successfully.');
    }

    private function ensureOwnsEvent(Event $event): void
    {
        abort_unless($event->user_id === Auth::id(), 404);
    }

    /**
     * @return array<string, int|string|null>
     */
    private function serializeEvent(Event $event): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'event_date' => $event->event_date?->toDateString(),
            'event_date_label' => $event->event_date?->format('l, d M Y'),
            'location' => $event->location,
            'edit_url' => route('events.edit', $event),
        ];
    }
}
