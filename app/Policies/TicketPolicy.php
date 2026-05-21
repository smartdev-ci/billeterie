<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function validate(User $user): bool
    {
        return in_array($user->role, ['admin', 'organizer'], true);
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id || $ticket->customer_email === $user->email;
    }

    public function download(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket);
    }
}