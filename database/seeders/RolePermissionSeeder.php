<?php

// database/seeders/RolePermissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view dashboard',

            'view cameras',
            'create cameras',
            'edit cameras',
            'delete cameras',

            'view faces',
            'create faces',
            'edit faces',
            'delete faces',

            'view alerts',
            'delete alerts',

            'view videos',
            'download videos',
            'delete videos',

            'view settings',
            'edit settings',

            'view users',
            'create users',
            'edit users',
            'delete users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view dashboard',
            'view cameras', 'create cameras', 'edit cameras', 'delete cameras',
            'view faces', 'create faces', 'edit faces', 'delete faces',
            'view alerts', 'delete alerts',
            'view videos', 'download videos', 'delete videos',
            'view settings', 'edit settings',
        ]);

        $viewer = Role::create(['name' => 'Viewer']);
        $viewer->givePermissionTo([
            'view dashboard',
            'view cameras',
            'view faces',
            'view alerts',
            'view videos',
        ]);

        // Assign first user as super admin
        if ($user = \App\Models\User::first()) {
            $user->assignRole('Super Admin');
        }
    }
}
