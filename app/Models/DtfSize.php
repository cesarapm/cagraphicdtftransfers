<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DtfSize extends Model
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
}
