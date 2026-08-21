<?php

namespace App\Policies;

use App\Models\DailyPerformance;
use App\Models\User;

class DailyPerformancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('view_performances');
    }

    public function view(User $user, DailyPerformance $dailyPerformance): bool
    {
        if ($user->isAdmin() || $user->hasPermissionTo('view_performances')) return true;
        return $dailyPerformance->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, DailyPerformance $dailyPerformance): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, DailyPerformance $dailyPerformance): bool
    {
        return $user->isAdmin();
    }
}
