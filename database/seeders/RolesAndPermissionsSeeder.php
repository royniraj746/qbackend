<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 🟢 IMPORTANT
        $guard = 'sanctum';

        // 🔹 Modules & actions
        $modules = [
            'dashboard' => ['view'],

            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'assign'],

            'customers' => ['view', 'create', 'edit', 'delete'],

            'quotations' => [
                'view',
                'create',
                'edit',
                'delete',
                'approve'
            ],

            'reports' => ['view'],
        ];

        // 🔹 Create permissions
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => $guard,
                ]);
            }
        }

        // 🔹 Create roles
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => $guard,
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'manager',
            'guard_name' => $guard,
        ]);

        $staff = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => $guard,
        ]);

        // 🔹 Admin → ALL permissions
        $admin->syncPermissions(
            Permission::where('guard_name', $guard)->get()
        );

        // 🔹 Manager → selective
        $manager->syncPermissions([
            'dashboard.view',
            'customers.view',
            'customers.create',
            'customers.edit',
            'quotations.view',
            'quotations.create',
            'quotations.edit',
            'quotations.approve',
            'reports.view',
        ]);

        // 🔹 Staff → limited
        $staff->syncPermissions([
            'dashboard.view',
            'quotations.view',
            'quotations.create',
        ]);
    }
}
