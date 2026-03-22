<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->is_admin) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user !== null;
    }

    public function view(?User $user, Event $event): bool
    {
        if ($user !== null && $event->user_id === $user->id) {
            return true;
        }

        if ($event->visibility === Event::VISIBILITY_PUBLIC) {
            return true;
        }

        return $user !== null && $event->visibility === Event::VISIBILITY_WORKSPACE;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, Event $event): bool
    {
        return $event->user_id === $user->id;
    }

    public function delete(User $user, Event $event): bool
    {
        return $event->user_id === $user->id;
    }

    public function sendReminder(User $user, Event $event): bool
    {
        return $event->user_id === $user->id;
    }
}
