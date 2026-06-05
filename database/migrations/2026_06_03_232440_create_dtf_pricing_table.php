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
        Schema::create('dtf_pricing', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "22x120", "22x60", "13x19"
            $table->decimal('width', 8, 2); // inches
            $table->decimal('height', 8, 2); // inches
            $table->decimal('base_price', 10, 2); // Price for this sheet size
            $table->decimal('min_coverage_discount', 5, 2)->default(0); // Discount % if coverage > threshold
            $table->decimal('coverage_threshold', 5, 2)->default(80); // Coverage % to get discount
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dtf_pricing');
    }
};
