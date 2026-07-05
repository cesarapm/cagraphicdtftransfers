<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'dtf_size_id',
        'dtf_gang_id',
        'sheet_size_id',
        'gang_sheet_id',
        'item_type',
        'product_name',
        'quantity',
        'unit_price',
        'total',
        'image',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function dtfSize(): BelongsTo
    {
        return $this->belongsTo(DtfSize::class);
    }

    public function dtfGang(): BelongsTo
    {
        return $this->belongsTo(DtfGang::class);
    }

    public function sheetSize(): BelongsTo
    {
        return $this->belongsTo(SheetSize::class);
    }

    public function gangSheet(): BelongsTo
    {
        return $this->belongsTo(GangSheet::class);
    }
}
