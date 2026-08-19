<?php

namespace App\Policies;

use App\Models\MediaFile;
use App\Models\User;

class MediaFilePolicy
{
    public function view(User $user, MediaFile $file): bool
    {
        // Permite ver el archivo si pertenece al usuario.
        return $file->user_id === $user->id;
    }

    public function download(User $user, MediaFile $file): bool
    {
        // Permite descargar el archivo si pertenece al usuario.
        return $file->user_id === $user->id;
    }

    public function delete(User $user, MediaFile $file): bool
    {
        // Permite eliminar el archivo si pertenece al usuario.
        return $file->user_id === $user->id;
    }
}
