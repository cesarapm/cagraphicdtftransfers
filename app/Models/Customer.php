<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'last_ordered_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_ordered_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the orders for the customer
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the gang sheets for the customer
     */
    public function gangSheets(): HasMany
    {
        return $this->hasMany(GangSheet::class);
    }

    /**
     * Get the discount codes used by the customer
     */
    public function discountCodeUsages(): HasMany
    {
        return $this->hasMany(DiscountCodeUsage::class);
    }
}