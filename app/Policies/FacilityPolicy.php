<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('facilities.view');
    }

    public function view(User $user, Facility $facility): bool
    {
        return $user->hasPermissionTo('facilities.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('facilities.manage');
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->hasPermissionTo('facilities.manage');
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->hasPermissionTo('facilities.manage');
    }
}
