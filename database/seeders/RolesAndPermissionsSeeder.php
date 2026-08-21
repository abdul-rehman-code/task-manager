<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            // User Permissions
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Department Permissions
            'view_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',

            // Task Permissions
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'delete_tasks',
            'assign_tasks',

            // Performance & Report Permissions
            'view_performances',
            'view_reports',
            'view_analytics',

            // Roles & Permissions
            'manage_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Admin gets all permissions
        $adminRole->syncPermissions(Permission::all());

        // Manager gets task, user viewing, reports and department permissions
        $managerRole->syncPermissions([
            'view_users',
            'view_departments',
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'assign_tasks',
            'view_performances',
            'view_reports',
        ]);

        // Employee gets task viewing & status update permissions
        $employeeRole->syncPermissions([
            'view_tasks',
            'view_performances',
        ]);

        // Sync existing users roles
        $users = User::all();
        foreach ($users as $user) {
            $roleName = $user->role ?? 'employee';
            if (in_array($roleName, ['admin', 'manager', 'employee'])) {
                $user->syncRoles([$roleName]);
            }
        }
    }
}
