<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('view_departments');
    }

    public function view(User $user, Department $department): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('view_departments');
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('create_departments');
    }

    public function update(User $user, Department $department): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('edit_departments');
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('delete_departments');
    }
}
