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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relationship - funciona con cualquier tabla
            $table->morphs('promotionable'); // Crea: promotionable_id, promotionable_type
            
            $table->string('titulo');
            $table->string('descripcion')->nullable();
            
            // Tipo de descuento: 'percentage' o 'fixed'
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 8, 2); // 10.00 para 10% o $10 fijos
            
            $table->date('inicio')->nullable();
            $table->date('fin')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Índice para búsquedas rápidas por estado
            $table->index('is_active');
            // morphs() ya crea automáticamente un índice en promotionable_type y promotionable_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
