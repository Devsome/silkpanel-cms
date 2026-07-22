<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webmall_category_items', function (Blueprint $table) {
            $table->unsignedInteger('amount')->default(1)->after('price_value');
        });

        Schema::table('webmall_purchases', function (Blueprint $table) {
            $table->unsignedInteger('amount')->default(1)->after('price_value');
        });
    }

    public function down(): void
    {
        Schema::table('webmall_category_items', function (Blueprint $table) {
            $table->dropColumn('amount');
        });

        Schema::table('webmall_purchases', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
