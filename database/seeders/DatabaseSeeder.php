<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call roles seeder first
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // User::factory(10)->create();

        // Create a test admin user
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $adminUser->assignRole('Admin');

        // Create a test customer user
        $customerUser = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
        ]);
        $customerUser->assignRole('Customer');
    }
}
