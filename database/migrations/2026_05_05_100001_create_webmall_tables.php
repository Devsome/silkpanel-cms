<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webmall_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('webmall_category_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webmall_category_id')->constrained('webmall_categories')->cascadeOnDelete();
            $table->unsignedInteger('ref_item_id');        // ID from RefObjCommon
            $table->string('item_name_snapshot')->nullable(); // cached display name
            $table->string('price_type');                  // 'silk_own','silk_gift','silk_point','1','3','gold'
            $table->unsignedInteger('price_value');
            $table->boolean('is_hot')->default(false);
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_until')->nullable();
            $table->unsignedInteger('stock')->nullable();  // null = unlimited
            $table->unsignedInteger('sold')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('webmall_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('character_id');
            $table->string('character_name');
            $table->foreignId('webmall_category_item_id')->constrained('webmall_category_items');
            $table->unsignedInteger('ref_item_id');
            $table->string('item_name');
            $table->string('price_type');
            $table->unsignedInteger('price_value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webmall_purchases');
        Schema::dropIfExists('webmall_category_items');
        Schema::dropIfExists('webmall_categories');
    }
};
