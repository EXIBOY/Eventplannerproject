<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
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

    public function store(EventRequest $request): RedirectResponse
    {
        $request->user()->events()->create($request->validated());

        return redirect()
            ->route('events.index')
            ->with('status', 'Event created successfully.');
    }

    public function edit(Event $event): View
    {
        $this->ensureOwnsEvent($event);

        return view('events.edit', compact('event'));
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $this->ensureOwnsEvent($event);
        $event->update($request->validated());

        return redirect()
            ->route('events.index')
            ->with('status', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->ensureOwnsEvent($event);
        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('status', 'Event deleted successfully.');
    }

    private function ensureOwnsEvent(Event $event): void
    {
        abort_unless($event->user_id === Auth::id(), 404);
    }
}
