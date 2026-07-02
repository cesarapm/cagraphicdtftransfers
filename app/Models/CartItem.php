<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'dtf_size_id',
        'quantity',
        'image_path',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'total_price' => 'float',
    ];

    /**
     * Relationship with DtfSize
     */
    public function dtfSize()
    {
        return $this->belongsTo(DtfSize::class);
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the file path to the uploaded image
     */
    public function getImageUrl()
    {
        return asset('storage/' . $this->image_path);
    }

    /**
     * Calculate and update total price
     */
    public function updateTotalPrice(): self
    {
        $unitPrice = (float) $this->unit_price ?? 0;
        $quantity = (int) $this->quantity ?? 1;
        
        $this->setAttribute('total_price', $unitPrice * $quantity);
        $this->save();
        
        return $this;
    }
}
