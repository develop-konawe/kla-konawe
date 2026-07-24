<?php

namespace App\Policies;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GalleryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('posts.view');
    }

    public function view(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo('posts.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('posts.create');
    }

    public function update(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo('posts.update');
    }

    public function delete(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo('posts.update');
    }
}
