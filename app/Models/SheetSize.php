<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SheetSize extends Model
{
    protected $fillable = [
        'name',
        'width',
        'height',
        'unit',
        'price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scope para obtener solo tamaños activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope para filtrar por unidad
    public function scopeByUnit($query, $unit)
    {
        return $query->where('unit', $unit);
    }

    // Scope ordenar por orden de visualización
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }
}
