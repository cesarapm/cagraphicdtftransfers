<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'gang_sheet_id')) {
                $table->foreignId('gang_sheet_id')
                    ->nullable()
                    ->constrained('gang_sheets')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'gang_sheet_id')) {
                $table->dropForeignKeyIfExists(['gang_sheet_id']);
                $table->dropColumn('gang_sheet_id');
            }
        });
    }
};
