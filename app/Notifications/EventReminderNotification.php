<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Event $event,
    ) {
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(sprintf('Reminder: %s', $this->event->title))
            ->greeting('Event reminder')
            ->line(sprintf('%s is scheduled for %s.', $this->event->title, $this->event->event_date?->format('l, d M Y')))
            ->line(sprintf('Time: %s', $this->event->timeLabel()))
            ->line(sprintf('Location: %s', $this->event->location))
            ->action('Open event', route('events.show', $this->event))
            ->line('This reminder was sent from Event Planner.');
    }
}
