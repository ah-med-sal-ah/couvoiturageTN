<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;

class PublicationPolicy
{
    /**
     * Determine whether the user owns the publication and may therefore
     * modify it - currently used to gate the reservation availability
     * toggle (see PublicationController::updateReservation).
     */
    public function update(User $user, Publication $publication): bool
    {
        return $user->id === $publication->user_id;
    }
}
