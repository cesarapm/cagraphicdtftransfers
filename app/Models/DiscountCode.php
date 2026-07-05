<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class DiscountCode extends Model
{
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'max_uses',
        'per_user_limit',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'discount_value' => 'decimal:2',
    ];

    /**
     * Convertir código a mayúsculas
     */
    protected function code(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper($value),
        );
    }

    /**
     * Relación con los usos del código
     */
    public function usages(): HasMany
    {
        return $this->hasMany(DiscountCodeUsage::class);
    }

    /**
     * Scope para obtener códigos activos
     */
    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            });
    }

    /**
     * Validar si el código es válido para un cliente
     */
    public function isValidForCustomer($customerId): array
    {
        $errors = [];

        // Verificar si está activo
        if (!$this->is_active) {
            $errors[] = 'Este código de descuento no está disponible.';
        }

        // Verificar fechas de validez
        if ($this->valid_from && $this->valid_from > now()) {
            $errors[] = 'Este código aún no es válido.';
        }

        if ($this->valid_until && $this->valid_until < now()) {
            $errors[] = 'Este código ha expirado.';
        }

        // Verificar límite de usos globales
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            $errors[] = 'Este código ha alcanzado su límite de usos.';
        }

        // Verificar si el cliente ya lo usó (per_user_limit)
        $customerUsageCount = $this->usages()->where('customer_id', $customerId)->count();
        if ($customerUsageCount >= $this->per_user_limit) {
            $errors[] = 'Ya has utilizado este código de descuento.';
        }

        return [
            'is_valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }

    /**
     * Calcular el descuento para un precio
     */
    public function calculateDiscount($price): float
    {
        $price = (float) $price;
        
        if ($this->discount_type === 'percentage') {
            return ($price * $this->discount_value) / 100;
        }
        
        return (float) $this->discount_value;
    }

    /**
     * Obtener precio final después del descuento
     */
    public function getFinalPrice($price): float
    {
        return max(0, (float) $price - $this->calculateDiscount($price));
    }

    /**
     * Marcar como usado por un cliente
     */
    public function markAsUsedByCustomer($customerId): void
    {
        // Registrar uso
        DiscountCodeUsage::create([
            'discount_code_id' => $this->id,
            'customer_id' => $customerId,
            'used_at' => now(),
        ]);

        // Incrementar contador
        $this->increment('used_count');
    }
}
