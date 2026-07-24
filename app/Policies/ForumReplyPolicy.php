<?php

namespace App\Policies;

use App\Models\ForumReply;
use App\Models\User;

class ForumReplyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('forum.manage');
    }
    public function view(User $user, ForumReply $reply): bool
    {
        return $user->hasPermissionTo('forum.manage');
    }
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('forum.manage');
    }
    public function update(User $user, ForumReply $reply): bool
    {
        return $user->hasPermissionTo('forum.manage');
    }
    public function delete(User $user, ForumReply $reply): bool
    {
        return $user->hasPermissionTo('forum.manage');
    }
}
