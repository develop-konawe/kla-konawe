<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PublicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('publications.manage');
    }

    public function view(User $user, Publication $publication): bool
    {
        return $user->hasPermissionTo('publications.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('publications.manage');
    }

    public function update(User $user, Publication $publication): bool
    {
        return $user->hasPermissionTo('publications.manage');
    }

    public function delete(User $user, Publication $publication): bool
    {
        return $user->hasPermissionTo('publications.manage');
    }
}
