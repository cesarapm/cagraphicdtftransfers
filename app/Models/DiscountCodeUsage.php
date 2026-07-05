<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountCodeUsage extends Model
{
    protected $fillable = [
        'discount_code_id',
        'user_id',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Relación con el código de descuento
     */
    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    /**
     * Relación con el usuario (Customer)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
