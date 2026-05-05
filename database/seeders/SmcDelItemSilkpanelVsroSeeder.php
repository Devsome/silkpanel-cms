<?php

namespace Database\Seeders;

use App\Enums\DatabaseNameEnums;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmcDelItemSilkpanelVsroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sql = file_get_contents(database_path('sql/smc_del_item_silkpanel_vsro.sql'));

        DB::connection(DatabaseNameEnums::SRO_SHARD->value)->unprepared($sql);

        $this->command->info('Procedure _SMC_DEL_ITEM_SILKPANEL_VSRO was successfully created/updated.');
    }
}
