<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Contribution management
            'view contributions',
            'create contributions',
            'create contributions for others',
            'approve contributions',
            'reject contributions',

            // Withdrawal management
            'view withdrawals',
            'create withdrawals',
            'approve withdrawals',
            'reject withdrawals',

            // Settings management
            'manage settings',

            // Reports
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - can manage users and view everything
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'view contributions',
            'create contributions',
            'create contributions for others',
            'view withdrawals',
            'create withdrawals',
            'view reports',
            'manage settings',
        ]);

        // Accountant - can approve/reject contributions and withdrawals
        $accountant = Role::create(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'view users',
            'view contributions',
            'create contributions',
            'create contributions for others',
            'approve contributions',
            'reject contributions',
            'view withdrawals',
            'create withdrawals',
            'approve withdrawals',
            'reject withdrawals',
            'view reports',
        ]);

        // Member - basic member permissions
        $member = Role::create(['name' => 'member']);
        $member->givePermissionTo([
            'view contributions',
            'create contributions',
            'view withdrawals',
            'view reports',
        ]);
    }
}
