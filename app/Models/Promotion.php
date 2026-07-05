<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Promotion extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'discount_type',
        'discount_value',
        'inicio',
        'fin',
        'is_active',
        'promotionable_type',
        'promotionable_id',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'inicio' => 'date',
        'fin' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the promotionable model (DtfGang, DtfSize, Product, etc.)
     */
    public function promotionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: obtener solo promociones activas y vigentes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('inicio')
                    ->orWhere('inicio', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('fin')
                    ->orWhere('fin', '>=', now());
            });
    }

    /**
     * Calcular el descuento aplicado a un precio
     */
    public function calculateDiscount($price): float
    {
        if ($this->discount_type === 'percentage') {
            return ($price * $this->discount_value) / 100;
        }

        return (float) $this->discount_value;
    }

    /**
     * Obtener el precio final después del descuento
     */
    public function getFinalPrice($price): float
    {
        return max(0, $price - $this->calculateDiscount($price));
    }
}
