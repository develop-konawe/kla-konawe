<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('pages.view');
    }

    public function view(User $user, Page $page): bool
    {
        return $user->hasPermissionTo('pages.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('pages.create');
    }

    public function update(User $user, Page $page): bool
    {
        return $user->hasPermissionTo('pages.update');
    }

    public function delete(User $user, Page $page): bool
    {
        return $user->hasPermissionTo('pages.update');
    }
}
