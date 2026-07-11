<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_storage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->integer('character_id');
            $table->string('character_name', 64);
            $table->unsignedBigInteger('item_id64'); // _Items.ID64 reference (stays in game DB)
            $table->unsignedInteger('ref_item_id')->default(0);
            $table->string('item_name', 128)->default('');
            $table->string('source_type', 32)->default('inventory'); // inventory, storage
            $table->unsignedTinyInteger('opt_level')->default(0); // plus level
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->json('item_data')->nullable(); // full snapshot for display
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('market_listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // seller
            $table->integer('character_id');
            $table->string('character_name', 64);
            $table->unsignedBigInteger('item_id64');
            $table->unsignedInteger('ref_item_id')->default(0);
            $table->string('item_name', 128)->default('');
            $table->unsignedTinyInteger('opt_level')->default(0);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->json('item_data')->nullable();
            $table->string('price_type', 32); // gold, silk_own, silk_gift, silk_point, 1, 3
            $table->unsignedBigInteger('price_amount');
            $table->string('fee_type', 16)->nullable(); // percent, fixed
            $table->unsignedBigInteger('fee_amount')->default(0);
            $table->text('description')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status', 16)->default('active'); // active, sold, expired, cancelled
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['status', 'expires_at']);
            $table->index(['status', 'ref_item_id']);
        });

        Schema::create('market_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('buyer_id');
            $table->integer('seller_character_id');
            $table->integer('buyer_character_id')->default(0);
            $table->string('seller_character_name', 64);
            $table->string('buyer_character_name', 64)->default('');
            $table->unsignedInteger('ref_item_id')->default(0);
            $table->string('item_name', 128)->default('');
            $table->unsignedTinyInteger('opt_level')->default(0);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->json('item_data')->nullable();
            $table->string('price_type', 32);
            $table->unsignedBigInteger('price_amount');
            $table->string('fee_type', 16)->nullable();
            $table->unsignedBigInteger('fee_amount')->default(0);
            $table->unsignedBigInteger('net_amount'); // price_amount - fee_amount
            $table->timestamps();

            $table->foreign('listing_id')->references('id')->on('market_listings');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('buyer_id')->references('id')->on('users');
            $table->index('seller_id');
            $table->index('buyer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_transactions');
        Schema::dropIfExists('market_listings');
        Schema::dropIfExists('web_storage');
    }
};
