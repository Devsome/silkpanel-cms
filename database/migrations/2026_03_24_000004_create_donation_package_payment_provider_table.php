<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donation_package_payment_provider', function (Blueprint $table) {
            $table->foreignId('donation_package_id')->constrained('donation_packages')->cascadeOnDelete();
            $table->foreignId('payment_provider_id')->constrained('payment_providers')->cascadeOnDelete();
            $table->primary(['donation_package_id', 'payment_provider_id'], 'package_provider_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_package_payment_provider');
    }
};
