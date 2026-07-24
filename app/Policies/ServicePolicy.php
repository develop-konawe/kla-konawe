<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('services.view');
    }

    public function view(User $user, Service $service): bool
    {
        return $user->hasPermissionTo('services.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('services.manage');
    }

    public function update(User $user, Service $service): bool
    {
        return $user->hasPermissionTo('services.manage');
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->hasPermissionTo('services.manage');
    }
}
