<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('complaints.view');
    }
    public function view(User $user, Complaint $complaint): bool
    {
        return $user->hasPermissionTo('complaints.view');
    }
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('complaints.manage');
    }
    public function update(User $user, Complaint $complaint): bool
    {
        return $user->hasPermissionTo('complaints.manage');
    }
    public function delete(User $user, Complaint $complaint): bool
    {
        return $user->hasPermissionTo('complaints.manage');
    }
}
