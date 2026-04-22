<?php

namespace Database\Seeders;

use App\Enums\UsergroupRoleEnums;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (Role::count() === 0) {
            Role::firstOrCreate(['name' => UsergroupRoleEnums::ADMIN->value]);
            Role::firstOrCreate(['name' => UsergroupRoleEnums::SUPPORTER->value]);
            Role::firstOrCreate(['name' => UsergroupRoleEnums::CUSTOMER->value]);
        }
    }
}
