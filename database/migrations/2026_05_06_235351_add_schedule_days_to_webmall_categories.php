<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('webmall_categories', function (Blueprint $table) {
            // JSON array of ISO weekday numbers: '1'=Mon ... '7'=Sun, null = every day
            $table->json('schedule_days')->nullable()->after('available_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webmall_categories', function (Blueprint $table) {
            $table->dropColumn('schedule_days');
        });
    }
};
