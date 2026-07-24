<?php

namespace App\Policies;

use App\Models\Statistic;
use App\Models\User;

class StatisticPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('statistics.view');
    }
    public function view(User $user, Statistic $statistic): bool
    {
        return $user->hasPermissionTo('statistics.view');
    }
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('statistics.manage');
    }
    public function update(User $user, Statistic $statistic): bool
    {
        return $user->hasPermissionTo('statistics.manage');
    }
    public function delete(User $user, Statistic $statistic): bool
    {
        return $user->hasPermissionTo('statistics.manage');
    }
    public function restore(User $user, Statistic $statistic): bool
    {
        return $user->hasPermissionTo('statistics.manage');
    }
    public function forceDelete(User $user, Statistic $statistic): bool
    {
        return $user->hasPermissionTo('statistics.manage');
    }
}
