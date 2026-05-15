<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_modals', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('image')->nullable();
            $table->json('buttons')->nullable(); // [{label, url, action, style}]
            $table->boolean('is_active')->default(true);

            // Display frequency
            $table->string('frequency')->default('once_per_session');
            // once_per_session | once_per_day | once_per_user | always

            // Conditions (JSON)
            $table->json('conditions')->nullable();
            // {
            //   "new_players_only": bool,
            //   "new_players_days": int,
            //   "min_character_level": int|null,
            //   "not_voted_today": bool,
            //   "pages": string[],  // empty = all pages
            // }

            // Time window
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();

            // Display settings
            $table->boolean('allow_backdrop_dismiss')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
        });

        Schema::create('user_modal_dismissals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_modal_id')->constrained()->cascadeOnDelete();
            $table->timestamp('dismissed_at')->useCurrent();

            $table->index(['user_id', 'session_modal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_modal_dismissals');
        Schema::dropIfExists('session_modals');
    }
};
