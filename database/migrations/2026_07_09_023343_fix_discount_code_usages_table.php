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
        Schema::table('discount_code_usages', function (Blueprint $table) {
            // Remover el unique constraint si existe para permitir múltiples usos por cliente
            // Dejar solo el índice compuesto para consultas rápidas
            try {
                $table->dropUnique(['discount_code_id', 'customer_id']);
            } catch (\Exception $e) {
                // Si no existe, continuar
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discount_code_usages', function (Blueprint $table) {
            // Restaurar el unique constraint
            $table->unique(['discount_code_id', 'customer_id']);
        });
    }
};
