<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('posts.view') || $user->hasPermissionTo('pages.view');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('posts.view') || $user->hasPermissionTo('pages.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('posts.create') || $user->hasPermissionTo('pages.create');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('posts.update') || $user->hasPermissionTo('pages.update');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('posts.update') || $user->hasPermissionTo('pages.update');
    }
}
