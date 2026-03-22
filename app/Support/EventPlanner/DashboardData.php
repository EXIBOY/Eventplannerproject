<?php

namespace App\Support\EventPlanner;

use App\Models\Event;
use App\Models\User;

class DashboardData
{
    /**
     * @return array<string, \Illuminate\Database\Eloquent\Collection<int, Event>|Event|int|null>
     */
    public function build(User $user): array
    {
        $baseQuery = Event::query()->ownedBy($user);

        $eventCount = (clone $baseQuery)->count();
        $upcomingEvents = (clone $baseQuery)->upcoming()->count();
        $pendingReminders = (clone $baseQuery)
            ->upcoming()
            ->whereNotNull('reminder_minutes')
            ->whereNull('reminder_sent_at')
            ->whereNotIn('status', [Event::STATUS_CANCELLED, Event::STATUS_COMPLETED])
            ->count();

        $recentActivity = (clone $baseQuery)
            ->past()
            ->orderedReverseChronologically()
            ->limit(3)
            ->get();

        $upcomingSchedule = (clone $baseQuery)
            ->upcoming()
            ->orderedChronologically()
            ->limit(4)
            ->get();

        $nextEvent = (clone $baseQuery)
            ->upcoming()
            ->orderedChronologically()
            ->first();

        return compact(
            'eventCount',
            'upcomingEvents',
            'pendingReminders',
            'recentActivity',
            'upcomingSchedule',
            'nextEvent',
        );
    }
}
