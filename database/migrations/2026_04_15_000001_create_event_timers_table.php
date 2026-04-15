<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_timers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('type', 20)->nullable();
            $table->json('days')->nullable();
            $table->json('hours')->nullable();
            $table->unsignedTinyInteger('hour')->nullable();
            $table->unsignedTinyInteger('min')->default(0);
            $table->string('time', 100)->nullable();
            $table->string('icon', 100)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_timers');
    }
};
