<?php

namespace App\Support\EventPlanner;

use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EventCalendarExporter
{
    public function forEvent(Event $event): string
    {
        return $this->buildCalendar(
            collect([$event]),
            sprintf('%s calendar export', $event->title),
        );
    }

    /**
     * @param  Collection<int, Event>  $events
     */
    public function forCollection(Collection $events, string $calendarName): string
    {
        return $this->buildCalendar($events, $calendarName);
    }

    /**
     * @param  Collection<int, Event>  $events
     */
    private function buildCalendar(Collection $events, string $calendarName): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Event Planner//Event Planner Calendar//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:'.$this->escapeText($calendarName),
        ];

        foreach ($events as $event) {
            $lines = [...$lines, ...$this->eventLines($event)];
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines)."\r\n";
    }

    /**
     * @return list<string>
     */
    private function eventLines(Event $event): array
    {
        $lines = [
            'BEGIN:VEVENT',
            'UID:'.$this->uidFor($event),
            'DTSTAMP:'.now()->utc()->format('Ymd\THis\Z'),
            'SUMMARY:'.$this->escapeText($event->title),
            'DESCRIPTION:'.$this->escapeText((string) ($event->description ?: 'No description added.')),
            'LOCATION:'.$this->escapeText($event->location),
        ];

        if ($event->start_time) {
            $lines[] = 'DTSTART:'.$event->startsAt()?->utc()->format('Ymd\THis\Z');
            $lines[] = 'DTEND:'.$event->endsAt()?->utc()->format('Ymd\THis\Z');
        } else {
            $lines[] = 'DTSTART;VALUE=DATE:'.$event->event_date?->format('Ymd');
            $lines[] = 'DTEND;VALUE=DATE:'.$event->event_date?->copy()->addDay()->format('Ymd');
        }

        $lines[] = 'STATUS:'.strtoupper($event->status === Event::STATUS_CANCELLED ? 'CANCELLED' : 'CONFIRMED');
        $lines[] = 'END:VEVENT';

        return $lines;
    }

    private function uidFor(Event $event): string
    {
        $host = parse_url(config('app.url') ?: 'http://localhost', PHP_URL_HOST) ?: 'localhost';

        return sprintf('event-%d-%s@%s', $event->id, Str::slug($event->title), $host);
    }

    private function escapeText(string $value): string
    {
        return str_replace(
            ["\\", ';', ',', "\n", "\r"],
            ['\\\\', '\;', '\,', '\n', ''],
            $value,
        );
    }
}
