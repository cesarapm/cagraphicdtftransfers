<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'order_number',
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip_code',
        'subtotal',
        'shipping_cost',
        'shipping_detalle',
        'tax',
        'total',
        'discount_code_id',
        'discount_amount',
        'discount_code',
        'metodo_pago',
        'payment_id',
        'envio',
        'status',
        'payment_status',
        'order_status',
        'observaciones',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'envio' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });

        static::updating(function ($order) {
            // Detectar cambio de estado a "shipped" (enviado)
            if ($order->isDirty('status') && $order->status === 'shipped') {
                $previousStatus = $order->getOriginal('status');

                // Solo reducir stock si no estaba en shipped antes
                if ($previousStatus !== 'shipped') {
                    // Reducir stock de los productos
                    foreach ($order->items as $item) {
                        if ($item->product) {
                            $item->product->decrement('stock', $item->quantity);
                        }
                    }
                }
            }

            // Si se cancela una orden que estaba en shipped, restaurar stock
            if ($order->isDirty('status') && $order->status === 'cancelled') {
                $previousStatus = $order->getOriginal('status');

                if ($previousStatus === 'shipped') {
                    // Restaurar stock
                    foreach ($order->items as $item) {
                        if ($item->product) {
                            $item->product->increment('stock', $item->quantity);
                        }
                    }
                }
            }
        });

        static::deleting(function ($order) {
            // Cargar items para asegurar que se ejecuten sus eventos deleting()
            $order->load('items');
            
            // Log::info('🗑️ Eliminando Order y sus OrderItems', [
            //     'order_id' => $order->id,
            //     'order_number' => $order->order_number,
            //     'items_count' => $order->items->count(),
            // ]);

            // Recolectar IDs de gang sheets a eliminar
            $gangSheetIds = [];
            
            // Eliminar cada item individualmente para que ejecute su evento deleting()
            // Esto borrará las imágenes de storage automáticamente
            foreach ($order->items as $item) {
                // Log::info('→ Eliminando OrderItem', [
                //     'item_id' => $item->id,
                //     'product_id' => $item->product_id,
                //     'image' => $item->image,
                //     'gang_sheet_id' => $item->gang_sheet_id,
                // ]);
                
                // Recolectar gang sheet IDs
                if ($item->gang_sheet_id) {
                    $gangSheetIds[] = $item->gang_sheet_id;
                }
                
                $item->delete();
            }

            // Eliminar gang sheets y sus imágenes
            if (!empty($gangSheetIds)) {
                // Log::info('📋 Eliminando gang sheets de la orden', [
                //     'order_id' => $order->id,
                //     'gang_sheet_ids' => $gangSheetIds,
                // ]);
                
                // Eliminar cada gang sheet individualmente para disparar eventos deleting()
                foreach ($gangSheetIds as $gangSheetId) {
                    $gangSheet = GangSheet::find($gangSheetId);
                    if ($gangSheet) {
                        // Log::info('→ Eliminando GangSheet', ['gang_sheet_id' => $gangSheetId]);
                        $gangSheet->delete(); // Dispara el evento deleting() que borra las imágenes
                    }
                }
            }

            // Log::info('✅ Todos los OrderItems y gang sheets eliminados correctamente', [
            //     'order_id' => $order->id,
            // ]);
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function pays(): HasMany
    {
        return $this->hasMany(Pay::class);
    }

    public function latestPay(): HasOne
    {
        return $this->hasOne(Pay::class)->latestOfMany();
    }

    public function getCustomerFullNameAttribute(): string
    {
        return "{$this->customer_first_name} {$this->customer_last_name}";
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aprobado' => 'Pago aprobado',
            'processing' => 'Procesando pedido',
            'pendiente' => 'Pendiente de pago',
            'pendiente_transferencia' => 'Pendiente de transferencia',
            'rechazado' => 'Pago rechazado',
            'cancelado' => 'Orden cancelada',
            'shipped' => 'Enviado',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusToneAttribute(): string
    {
        return match ($this->status) {
            'aprobado', 'delivered' => 'success',
            'processing' => 'processing',
            'pendiente', 'pendiente_transferencia', 'shipped' => 'warning',
            'rechazado', 'cancelado', 'cancelled' => 'error',
            default => 'info',
        };
    }

    public function getTrackingTokenAttribute(): string
    {
        $payload = implode('|', [
            $this->id,
            $this->order_number,
            strtolower((string) $this->customer_email),
        ]);

        return hash_hmac('sha256', $payload, (string) config('app.key'));
    }

    public function getTrackingUrlAttribute(): string
    {
        $frontendUrl = rtrim((string) config('app.frontend_url', config('app.url')), '/');

        return $frontendUrl . '/seguimiento-pedido/' . urlencode((string) $this->order_number) . '?token=' . $this->tracking_token;
    }
}
