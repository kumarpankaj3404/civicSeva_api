<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Flush cached roles/permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $citizen = Role::firstOrCreate(['name' => 'citizen', 'guard_name' => 'web']);
        $admin   = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']);

        // Create permissions
        $permissions = [
            'view schemes',
            'create schemes',
            'edit schemes',
            'delete schemes',
            'view applications',
            'manage applications',
            'view categories',
            'create categories',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $citizen->syncPermissions(['view schemes', 'view applications', 'view categories']);
        $admin->syncPermissions($permissions);

        $this->command->info('Roles and permissions seeded.');
    }
}
