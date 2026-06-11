<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedure_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('procedure_name')->nullable();
            $table->string('database_connection')->nullable();
            $table->json('input_payload')->nullable();
            $table->json('mapped_payload')->nullable();
            $table->json('context')->nullable();
            $table->boolean('success')->default(false);
            $table->boolean('fallback_used')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['action', 'created_at']);
            $table->index('success');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedure_logs');
    }
};
