<?php

namespace App\Support\EventPlanner;

use App\Models\Event;
use App\Models\User;

class EventLibraryData
{
    /**
     * @return array<string, array<string, int>|\Illuminate\Database\Eloquent\Collection<int, Event>>
     */
    public function build(User $user): array
    {
        $baseQuery = Event::query()->ownedBy($user);

        $upcomingEvents = (clone $baseQuery)
            ->upcoming()
            ->orderedChronologically()
            ->get();

        $pastEvents = (clone $baseQuery)
            ->past()
            ->orderedReverseChronologically()
            ->get();

        $statusBreakdown = array_replace(
            array_fill_keys(array_keys(Event::statusOptions()), 0),
            (clone $baseQuery)
                ->selectRaw('status, COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
                ->map(fn (mixed $value): int => (int) $value)
                ->all(),
        );

        return compact('upcomingEvents', 'pastEvents', 'statusBreakdown');
    }
}
