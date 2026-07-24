<?php

namespace App\Policies;

use App\Models\ForumTopic;
use App\Models\User;

class ForumTopicPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('forum.manage');
    }
    public function view(User $user, ForumTopic $topic): bool
    {
        return $user->hasPermissionTo('forum.manage');
    }
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('forum.manage');
    }
    public function update(User $user, ForumTopic $topic): bool
    {
        return $user->hasPermissionTo('forum.manage');
    }
    public function delete(User $user, ForumTopic $topic): bool
    {
        return $user->hasPermissionTo('forum.manage');
    }
}
