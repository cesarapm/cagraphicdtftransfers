<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class SheetSize extends Model
{
    protected $fillable = [
        'name',
        'width',
        'height',
        'unit',
        'price',
        'description', // descripción opcional
        'image_path', // ruta de la imagen opcional
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

    
    /**
     * Get the promotion for this SheetSize
     */
    public function promotion(): MorphOne
    {
        return $this->morphOne(Promotion::class, 'promotionable');
    }

    /**
     * Get all promotions for this SheetSize
     */
    public function promotions(): MorphMany
    {
        return $this->morphMany(Promotion::class, 'promotionable');
    }

    /**
     * Get active promotion if exists
     */
    public function activePromotion()
    {
        return $this->promotion()->active();
    }
        /**
     * Delete the old image when updating and when deleting the record
     */
    protected static function booted(): void
    {
        static::updating(function ($model) {
            // Si la imagen cambió, eliminar la antigua
            if ($model->isDirty('image_path')) {
                $oldImage = $model->getOriginal('image_path');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        static::deleting(function ($model) {
            // Eliminar la imagen cuando se borra el registro
            if ($model->image_path && Storage::disk('public')->exists($model->image_path)) {
                Storage::disk('public')->delete($model->image_path);
            }
        });
                static::creating(function ($model) {
            if (is_null($model->sort_order)) {
                $maxSortOrder = static::max('sort_order');
                $model->sort_order = $maxSortOrder !== null ? $maxSortOrder + 1 : 1;
            }
        });
    }
}
