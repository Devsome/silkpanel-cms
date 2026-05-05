<?php

namespace Database\Seeders;

use Database\Seeders\RolesAndPermissionsSeeder;
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
        // Call roles seeder first, also called in the installer
        $this->call([
            RolesAndPermissionsSeeder::class,
            SettingsSeeder::class,
            PaymentProviderSeeder::class,
            SmcDelItemSilkpanelVsroSeeder::class,
            SmcDelItemSilkpanelIsroSeeder::class,
            AddItemSilkpanelAutoVsroSeeder::class,
            AddItemSilkpanelAutoIsroSeeder::class,
        ]);
    }
}
