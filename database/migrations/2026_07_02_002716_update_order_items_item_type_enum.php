<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL ENUM requires special handling for modifications
        // We'll change it to a VARCHAR to support extensibility
        Schema::table('order_items', function (Blueprint $table) {
            // Change the enum column to varchar(50) to support all types
            DB::statement("ALTER TABLE order_items CHANGE COLUMN item_type item_type VARCHAR(50) DEFAULT 'product'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum if needed
        Schema::table('order_items', function (Blueprint $table) {
            DB::statement("ALTER TABLE order_items CHANGE COLUMN item_type item_type ENUM('product', 'dtf') DEFAULT 'product'");
        });
    }
};
