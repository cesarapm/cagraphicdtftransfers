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
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'sheet_size_id')) {
                $table->foreignId('sheet_size_id')
                    ->nullable()
                    ->constrained('sheet_sizes')
                    ->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'sheet_size_id')) {
                $table->dropForeignIdFor('SheetSize');
                $table->dropColumn('sheet_size_id');
            }
        });
    }
};
