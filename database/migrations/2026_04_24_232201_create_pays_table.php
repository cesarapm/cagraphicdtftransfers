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
        Schema::create('pays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->string('payment_id')->nullable();
            $table->string('id_pago')->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('monto_transaccion', 10, 2)->nullable();
            $table->decimal('monto_recibido_neto', 10, 2)->nullable();
            $table->decimal('monto_a_pagar', 10, 2)->nullable();
            $table->string('codigo_autorizacion')->nullable();
            $table->string('estado')->nullable();
            $table->string('fecha_aprobacion')->nullable();
            $table->string('fecha_creacion')->default(now()->toDateTimeString());
            $table->string('fecha_ultima_actualizacion')->nullable();
            $table->string('metodo_pago')->nullable();
            $table->string('numero_tarjeta')->nullable();
            $table->string('ip_direccion')->nullable();
            $table->text('url_notificacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pays');
    }
};
