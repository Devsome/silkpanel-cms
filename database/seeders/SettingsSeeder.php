<?php

namespace Database\Seeders;

use App\Helpers\SettingHelper;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SettingHelper::seedDefaults();

        $this->command->info('Settings seeded successfully!');
    }
}
