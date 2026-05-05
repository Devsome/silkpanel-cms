<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_logs', function (Blueprint $table) {
            $table->unsignedInteger('data')->nullable()->change();
            $table->unsignedTinyInteger('opt_level')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('item_logs', function (Blueprint $table) {
            $table->unsignedInteger('data')->default(1)->change();
            $table->unsignedTinyInteger('opt_level')->default(0)->change();
        });
    }
};
