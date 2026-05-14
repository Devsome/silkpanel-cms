<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add soft-delete column to webmall_category_items
        Schema::table('webmall_category_items', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Change the FK on webmall_purchases so that deleting an item
        // sets the reference to NULL instead of blocking the delete.
        // This preserves the purchase history record while allowing item removal.
        Schema::table('webmall_purchases', function (Blueprint $table) {
            $table->dropForeign(['webmall_category_item_id']);
            $table->unsignedBigInteger('webmall_category_item_id')->nullable()->change();
            $table->foreign('webmall_category_item_id')
                ->references('id')
                ->on('webmall_category_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Restore the original non-nullable FK (only possible if no NULLs exist)
        Schema::table('webmall_purchases', function (Blueprint $table) {
            $table->dropForeign(['webmall_category_item_id']);
            $table->unsignedBigInteger('webmall_category_item_id')->nullable(false)->change();
            $table->foreign('webmall_category_item_id')
                ->references('id')
                ->on('webmall_category_items');
        });

        Schema::table('webmall_category_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
