<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pay extends Model
{
        protected $fillable = [
        'order_id',
        'payment_id',
        'id_pago',
        'descripcion',
        'monto_transaccion',
        'monto_recibido_neto',
        'monto_a_pagar',
        'codigo_autorizacion',
        'estado',
        'fecha_aprobacion',
        'fecha_creacion',
        'fecha_ultima_actualizacion',
        'metodo_pago',
        'numero_tarjeta',
        'ip_direccion',
        'url_notificacion',

    ];

    protected $casts = [
        'monto_transaccion' => 'decimal:2',
        'monto_recibido_neto' => 'decimal:2',
        'monto_a_pagar' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
