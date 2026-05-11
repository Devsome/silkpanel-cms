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
            $table->dateTime('available_from')->nullable()->after('enabled');
            $table->dateTime('available_until')->nullable()->after('available_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webmall_categories', function (Blueprint $table) {
            $table->dropColumn(['available_from', 'available_until']);
        });
    }
};
