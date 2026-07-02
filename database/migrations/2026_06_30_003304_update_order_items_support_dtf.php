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
            // Agregar product_id si no existe
            if (!Schema::hasColumn('order_items', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('order_id');
            }
            
            // Agregar dtf_size_id para DTF transfers
            if (!Schema::hasColumn('order_items', 'dtf_size_id')) {
                $table->unsignedBigInteger('dtf_size_id')->nullable()->after('product_id');
            }
            
            // Agregar tipo de item
            if (!Schema::hasColumn('order_items', 'item_type')) {
                $table->enum('item_type', ['product', 'dtf'])->default('product')->after('dtf_size_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumnIfExists(['product_id', 'dtf_size_id', 'item_type']);
        });
    }
};
