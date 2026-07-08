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
            // Agregar constraint a dtf_size_id si no existe
            try {
                $table->foreign('dtf_size_id')
                    ->references('id')
                    ->on('dtf_sizes')
                    ->nullOnDelete();
            } catch (\Exception $e) {
                // La constraint ya existe, continuar
            }

            // Agregar constraint a dtf_gang_id si no existe
            try {
                $table->foreign('dtf_gang_id')
                    ->references('id')
                    ->on('dtf_gangs')
                    ->nullOnDelete();
            } catch (\Exception $e) {
                // La constraint ya existe, continuar
            }

            // Agregar constraint a sheet_size_id si no existe
            try {
                $table->foreign('sheet_size_id')
                    ->references('id')
                    ->on('sheet_sizes')
                    ->nullOnDelete();
            } catch (\Exception $e) {
                // La constraint ya existe, continuar
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['dtf_size_id']);
            $table->dropForeignKeyIfExists(['dtf_gang_id']);
            $table->dropForeignKeyIfExists(['sheet_size_id']);
        });
    }
};
