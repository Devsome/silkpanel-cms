<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::set('active_template', '', 'text', 'Active Template', 'The currently active frontend template (empty = default views)');
    }

    public function down(): void
    {
        Setting::deleteByKey('active_template');
    }
};
