<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DtfPricing extends Model
{
    protected $table = 'dtf_pricing';

    protected $fillable = [
        'name',
        'width',
        'height',
        'base_price',
        'min_coverage_discount',
        'coverage_threshold',
        'is_active',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'base_price' => 'decimal:2',
        'min_coverage_discount' => 'decimal:2',
        'coverage_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Calculate final price with discount if applicable
     */
    public function calculatePrice(float $coveragePercentage): float
    {
        $price = $this->base_price;

        // Apply discount if coverage exceeds threshold
        if ($coveragePercentage >= $this->coverage_threshold) {
            $discount = ($this->min_coverage_discount / 100) * $price;
            $price -= $discount;
        }

        return round($price, 2);
    }

    /**
     * Get active pricing options ordered
     */
    public static function getActivePricing()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Find pricing by dimensions
     */
    public static function findByDimensions(float $width, float $height)
    {
        return self::where('width', $width)
            ->where('height', $height)
            ->where('is_active', true)
            ->first();
    }
}
