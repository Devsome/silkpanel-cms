<?php

use App\Enums\WebmallItemTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webmall_category_items', function (Blueprint $table) {
            $table->string('item_type')->default(WebmallItemTypeEnum::REGULAR_ITEM->value)->after('item_name_snapshot');
            $table->foreignId('procedure_mapping_id')
                ->nullable()
                ->after('item_type')
                ->constrained('procedure_mappings')
                ->nullOnDelete();
            $table->string('custom_image_path')->nullable()->after('procedure_mapping_id');
            $table->string('custom_database_connection')->nullable()->after('custom_image_path');
            $table->string('custom_procedure_name')->nullable()->after('custom_database_connection');
            $table->json('custom_parameters')->nullable()->after('custom_procedure_name');

            $table->index('item_type');
        });

        Schema::table('webmall_purchases', function (Blueprint $table) {
            $table->string('item_type')->default(WebmallItemTypeEnum::REGULAR_ITEM->value)->after('webmall_category_item_id');
            $table->string('status')->default('completed')->after('price_value');
            $table->foreignId('procedure_mapping_id')
                ->nullable()
                ->after('status')
                ->constrained('procedure_mappings')
                ->nullOnDelete();
            $table->foreignId('procedure_log_id')
                ->nullable()
                ->after('procedure_mapping_id')
                ->constrained('procedure_logs')
                ->nullOnDelete();
            $table->string('procedure_name_snapshot')->nullable()->after('procedure_log_id');
            $table->text('procedure_error_message')->nullable()->after('procedure_name_snapshot');

            $table->index('item_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('webmall_purchases', function (Blueprint $table) {
            $table->dropIndex(['item_type']);
            $table->dropIndex(['status']);

            $table->dropConstrainedForeignId('procedure_log_id');
            $table->dropConstrainedForeignId('procedure_mapping_id');
            $table->dropColumn([
                'item_type',
                'status',
                'procedure_name_snapshot',
                'procedure_error_message',
            ]);
        });

        Schema::table('webmall_category_items', function (Blueprint $table) {
            $table->dropIndex(['item_type']);

            $table->dropConstrainedForeignId('procedure_mapping_id');
            $table->dropColumn([
                'item_type',
                'custom_image_path',
                'custom_database_connection',
                'custom_procedure_name',
                'custom_parameters',
            ]);
        });
    }
};
