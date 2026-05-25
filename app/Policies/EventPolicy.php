<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;

class EventPolicy
{
    /**
     * Determine if the user can view any events (public access)
     */
    public function viewAny(?User $user): bool
    {
        return true; // Public access for viewing events
    }

    /**
     * Determine if the user can view a specific event
     */
    public function view(?User $user, Event $event): bool
    {
        return true; // Public access for viewing active events
    }

    /**
     * Determine if the user can create events
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ORGANIZER, User::ROLE_ADMIN]);
    }

    /**
     * Determine if the user can update the event
     */
    public function update(User $user, Event $event): bool
    {
        return in_array($user->role, [User::ROLE_ORGANIZER, User::ROLE_ADMIN]);
    }

    /**
     * Determine if the user can delete the event
     */
    public function delete(User $user, Event $event): bool
    {
        return in_array($user->role, [User::ROLE_ORGANIZER, User::ROLE_ADMIN]);
    }

    /**
     * Determine if the user can manage tickets for this event
     */
    public function manageTickets(User $user, Event $event): bool
    {
        return in_array($user->role, [User::ROLE_ORGANIZER, User::ROLE_ADMIN]);
    }
}
