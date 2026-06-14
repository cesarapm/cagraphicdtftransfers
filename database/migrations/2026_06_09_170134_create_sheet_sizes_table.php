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
        Schema::create('sheet_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ej: "22 x 120 feet"
            $table->decimal('width', 10, 2); // ancho
            $table->decimal('height', 10, 2); // alto
            $table->enum('unit', ['feet', 'inches']); // unidad de medida
            $table->decimal('price', 10, 2); // precio
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheet_sizes');
    }
};
