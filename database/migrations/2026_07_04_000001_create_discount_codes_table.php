<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Ej: "SUMMER2024", "WELCOME10"
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']); // percentage (%) o fixed ($)
            $table->decimal('discount_value', 8, 2); // 15 para 15%, 5.99 para $5.99
            $table->integer('max_uses')->nullable(); // null = ilimitado
            $table->integer('used_count')->default(0); // Contador de usos totales
            $table->integer('per_user_limit')->default(1); // Cuántas veces por usuario
            $table->boolean('is_active')->default(true);
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
