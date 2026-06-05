<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Renombrar 'status' a 'order_status' (estado del pedido/envío)
            $table->renameColumn('status', 'order_status');
        });

        // Agregar nuevas columnas después del rename
        Schema::table('orders', function (Blueprint $table) {
            // Estado del pago: pendiente_pago, pagado, rechazado, reembolsado
            $table->string('payment_status')->default('pendiente_pago')->after('order_status');
            
            // Observaciones del admin
            $table->text('observaciones')->nullable()->after('payment_status');
        });

        // Migrar datos existentes: mapear order_status antiguo al nuevo payment_status
        DB::table('orders')->update([
            'payment_status' => DB::raw("CASE 
                WHEN order_status = 'aprobado' THEN 'pagado'
                WHEN order_status = 'rechazado' THEN 'rechazado'
                WHEN order_status = 'cancelado' THEN 'rechazado'
                WHEN order_status = 'pendiente_transferencia' THEN 'pendiente_pago'
                ELSE 'pendiente_pago'
            END"),
            'order_status' => DB::raw("CASE 
                WHEN order_status IN ('aprobado', 'pagado') THEN 'procesando'
                WHEN order_status = 'cancelado' THEN 'cancelado'
                ELSE 'pendiente'
            END")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'observaciones']);
            $table->renameColumn('order_status', 'status');
        });
    }
};
