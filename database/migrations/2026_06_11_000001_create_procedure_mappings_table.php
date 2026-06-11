<?php

use App\Enums\DatabaseNameEnums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedure_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('action')->unique();
            $table->string('action_label');
            $table->string('procedure_name')->nullable();
            $table->string('database_connection')->default(DatabaseNameEnums::SRO_SHARD->value);
            $table->json('parameter_map')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('use_fallback')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedure_mappings');
    }
};
