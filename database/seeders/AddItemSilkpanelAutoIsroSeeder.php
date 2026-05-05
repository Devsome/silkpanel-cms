<?php

namespace Database\Seeders;

use App\Enums\DatabaseNameEnums;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddItemSilkpanelAutoIsroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sql = file_get_contents(database_path('sql/add_item_silkpanel_auto_isro.sql'));

        DB::connection(DatabaseNameEnums::SRO_SHARD->value)->unprepared($sql);

        $this->command->info('Procedure _ADD_ITEM_SILKPANEL_AUTO_ISRO was successfully created/updated.');
    }
}
