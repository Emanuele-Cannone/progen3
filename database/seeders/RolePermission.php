<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions

        // Group
        Permission::create(['name' => 'edit-group']);
        Permission::create(['name' => 'delete-group']);
        Permission::create(['name' => 'publish-group']);
        Permission::create(['name' => 'create-group']);

        // User
        Permission::create(['name' => 'edit-user']);
        Permission::create(['name' => 'delete-user']);
        Permission::create(['name' => 'create-user']);


        // create roles and assign existing permissions
        $superAdmin     =   Role::create(['name' => 'super-admin']);
        $adminRole      =   Role::create(['name' => 'admin']);
        $helpDeskRole   =   Role::create(['name' => 'helpdesk']);
        $developerRole  =   Role::create(['name' => 'developer']);
        $accountantRole =   Role::create(['name' => 'accountant']);
        $superVisorRole =   Role::create(['name' => 'supervisor']);
        $teamLeaderRole =   Role::create(['name' => 'team-leader']);
        $operatorRole   =   Role::create(['name' => 'operator']);
        $customerRole   =   Role::create(['name' => 'customer']);

        $adminRole->givePermissionTo(
            'edit-group',
            'delete-group',
            'publish-group',
            'create-group',

            'edit-user',
            'delete-user',
            'create-user',
        );

        $accountantRole->givePermissionTo(
            'edit-user',
            'delete-user',
            'create-user',
        );


        User::all()->each(function ($user) use ($accountantRole) {
            $user->assignRole($accountantRole);
        });
    }
}
