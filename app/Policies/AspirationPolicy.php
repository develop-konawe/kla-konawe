<?php

namespace App\Policies;

use App\Models\Aspiration;
use App\Models\User;

class AspirationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('aspirations.view');
    }
    public function view(User $user, Aspiration $aspiration): bool
    {
        return $user->hasPermissionTo('aspirations.view');
    }
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('aspirations.manage');
    }
    public function update(User $user, Aspiration $aspiration): bool
    {
        return $user->hasPermissionTo('aspirations.manage');
    }
    public function delete(User $user, Aspiration $aspiration): bool
    {
        return $user->hasPermissionTo('aspirations.manage');
    }
}
