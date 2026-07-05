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
            // Agregar dtf_gang_id para gang sheets
            if (!Schema::hasColumn('order_items', 'dtf_gang_id')) {
                $table->unsignedBigInteger('dtf_gang_id')->nullable()->after('dtf_size_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'dtf_gang_id')) {
                $table->dropColumn('dtf_gang_id');
            }
        });
    }
};
