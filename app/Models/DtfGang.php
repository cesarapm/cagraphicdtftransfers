<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class DtfGang extends Model
{
    
    protected $fillable = [
        'name',
        'width',
        'height',
        'unit',
        'price',
        'is_active',
        'sort_order',
        'description', // descripción opcional
        'image_path', // ruta de la imagen opcional
    ];

    protected $casts = [
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the promotion for this DtfGang
     */
    public function promotion(): MorphOne
    {
        return $this->morphOne(Promotion::class, 'promotionable');
    }

    /**
     * Get active promotion if exists
     */
    public function activePromotion()
    {
        return $this->promotion()->active();
    }
}
