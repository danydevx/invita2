<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function view(User $user, SupportTicket $ticket): bool
    {
        // Permite ver el ticket si es propio o si tiene permiso de soporte.
        return $ticket->user_id === $user->id || $user->can('support.view');
    }

    public function reply(User $user, SupportTicket $ticket): bool
    {
        // Permite responder si es propio o si tiene permiso de soporte.
        return $ticket->user_id === $user->id || $user->can('support.reply');
    }

    public function update(User $user, SupportTicket $ticket): bool
    {
        // Permite actualizar el ticket solo con permisos administrativos.
        return $user->can('support.update');
    }

    public function close(User $user, SupportTicket $ticket): bool
    {
        // Permite cerrar el ticket solo con permisos administrativos.
        return $user->can('support.close');
    }
}
