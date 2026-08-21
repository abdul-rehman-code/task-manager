<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('view_tasks');
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->isAdmin() || $user->hasPermissionTo('view_tasks')) return true;
        return $task->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('create_tasks');
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->isAdmin() || $user->hasPermissionTo('edit_tasks')) return true;
        // Allows employees to update status of their own tasks
        return $task->assigned_to === $user->id; 
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('delete_tasks');
    }
}
