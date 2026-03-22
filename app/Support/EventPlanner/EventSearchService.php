<?php

namespace App\Support\EventPlanner;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Collection;

class EventSearchService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array{q: string, scope: string, timeframe: string, status: string, category: string, visibility: string, sort: string}
     */
    public function normalizeFilters(array $filters): array
    {
        $normalized = [
            'q' => trim((string) ($filters['q'] ?? '')),
            'scope' => (string) ($filters['scope'] ?? 'all'),
            'timeframe' => (string) ($filters['timeframe'] ?? 'all'),
            'status' => (string) ($filters['status'] ?? 'any'),
            'category' => (string) ($filters['category'] ?? 'any'),
            'visibility' => (string) ($filters['visibility'] ?? 'any'),
            'sort' => (string) ($filters['sort'] ?? 'soonest'),
        ];

        if (!in_array($normalized['scope'], ['all', 'mine', 'shared'], true)) {
            $normalized['scope'] = 'all';
        }

        if (!in_array($normalized['timeframe'], ['all', 'upcoming', 'past'], true)) {
            $normalized['timeframe'] = 'all';
        }

        if (!in_array($normalized['status'], array_merge(['any'], array_keys(Event::statusOptions())), true)) {
            $normalized['status'] = 'any';
        }

        if (!in_array($normalized['category'], array_merge(['any'], array_keys(Event::categoryOptions())), true)) {
            $normalized['category'] = 'any';
        }

        if (!in_array($normalized['visibility'], array_merge(['any'], array_keys(Event::visibilityOptions())), true)) {
            $normalized['visibility'] = 'any';
        }

        if (!in_array($normalized['sort'], ['soonest', 'latest', 'title'], true)) {
            $normalized['sort'] = 'soonest';
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function hasSearchIntent(array $filters): bool
    {
        $normalized = $this->normalizeFilters($filters);

        return $normalized['q'] !== ''
            || $normalized['scope'] !== 'all'
            || $normalized['timeframe'] !== 'all'
            || $normalized['status'] !== 'any'
            || $normalized['category'] !== 'any'
            || $normalized['visibility'] !== 'any';
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Event>
     */
    public function search(?User $viewer, array $filters): Collection
    {
        $normalized = $this->normalizeFilters($filters);

        if (!$this->hasSearchIntent($normalized)) {
            return collect();
        }

        $query = Event::query()
            ->with('user:id,name,email')
            ->visibleTo($viewer);

        if ($viewer !== null && $normalized['scope'] === 'mine') {
            $query->where('user_id', $viewer->id);
        } elseif ($viewer !== null && $normalized['scope'] === 'shared') {
            $query->where('user_id', '!=', $viewer->id);
        }

        if ($normalized['q'] !== '') {
            $query->searchTerm($normalized['q']);
        }

        if ($normalized['status'] !== 'any') {
            $query->where('status', $normalized['status']);
        }

        if ($normalized['category'] !== 'any') {
            $query->where('category', $normalized['category']);
        }

        if ($normalized['visibility'] !== 'any') {
            $query->where('visibility', $normalized['visibility']);
        }

        if ($normalized['timeframe'] === 'upcoming') {
            $query->upcoming();
        } elseif ($normalized['timeframe'] === 'past') {
            $query->past();
        }

        match ($normalized['sort']) {
            'latest' => $query->orderedReverseChronologically(),
            'title' => $query->orderBy('title')->orderBy('event_date'),
            default => $query->orderedChronologically(),
        };

        return $query->limit(12)->get();
    }
}
