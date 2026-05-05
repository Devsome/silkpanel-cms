<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('char_id')->nullable()->index();
            $table->string('char_name', 64)->nullable();
            $table->string('procedure', 64);
            $table->string('code_name', 128)->nullable();
            $table->unsignedInteger('ref_item_id')->nullable();
            $table->unsignedInteger('data')->nullable();
            $table->unsignedTinyInteger('opt_level')->nullable();
            $table->unsignedBigInteger('variance')->nullable();
            $table->boolean('success');
            $table->smallInteger('return_code');
            $table->string('destination', 10)->nullable();
            $table->unsignedSmallInteger('slot')->nullable();
            $table->unsignedBigInteger('new_item_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('success');
            $table->index('ref_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_logs');
    }
};
