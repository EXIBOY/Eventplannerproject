<?php

namespace App\Support\EventPlanner;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Illuminate\Support\Facades\Notification;

class EventReminderService
{
    public function send(Event $event, bool $force = false): bool
    {
        if (!$force) {
            if ($event->reminder_minutes === null || $event->reminder_sent_at !== null) {
                return false;
            }

            if (in_array($event->status, [Event::STATUS_CANCELLED, Event::STATUS_COMPLETED], true)) {
                return false;
            }
        }

        $event->loadMissing('user');

        $event->user->notify(new EventReminderNotification($event));

        if ($event->organizer_email && $event->organizer_email !== $event->user->email) {
            Notification::route('mail', [$event->organizer_email => ($event->organizer_name ?: $event->organizer_email)])
                ->notify(new EventReminderNotification($event));
        }

        $event->forceFill([
            'reminder_sent_at' => now(),
        ])->save();

        return true;
    }

    public function sendDueReminders(): int
    {
        $events = Event::query()
            ->with('user')
            ->whereNotNull('reminder_minutes')
            ->whereNull('reminder_sent_at')
            ->whereNotIn('status', [Event::STATUS_CANCELLED, Event::STATUS_COMPLETED])
            ->whereDate('event_date', '<=', now()->addDays(7)->toDateString())
            ->orderedChronologically()
            ->get();

        $sent = 0;

        foreach ($events as $event) {
            $reminderAt = $event->reminderAt();

            if ($reminderAt !== null && $reminderAt->lte(now()) && $this->send($event)) {
                $sent++;
            }
        }

        return $sent;
    }
}
