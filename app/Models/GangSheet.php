<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GangSheet extends Model
{
    protected $fillable = [
        'customer_id',
        'order_id',
        'name',
        'width',
        'height',
        'unit',
        'dpi',
        'images_data',
        'preview_path',
        'final_path',
        'total_area',
        'image_count',
        'status',
        'notes',
        // Pricing
        'price',
        'coverage_percentage',
        // Approval
        'requires_approval',
        'submitted_at',
        'approved_at',
        'approved_by',
        'approval_notes',
        // Payment
        'payment_status',
        'payment_id',
        'payment_method',
        'paid_at',
        // Production
        'production_status',
        'production_started_at',
        'completed_at',
        'tracking_number',
    ];

    protected $casts = [
        'images_data' => 'array',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'total_area' => 'decimal:2',
        'price' => 'decimal:2',
        'coverage_percentage' => 'decimal:2',
        'dpi' => 'integer',
        'image_count' => 'integer',
        'requires_approval' => 'boolean',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'production_started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the gang sheet
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order associated with the gang sheet
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the approver user (admin from Filament CRM)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calculate total area used
     */
    public function calculateTotalArea(): float
    {
        $totalArea = 0;
        foreach ($this->images_data as $image) {
            $totalArea += ($image['width'] ?? 0) * ($image['height'] ?? 0);
        }
        return $totalArea;
    }

    /**
     * Get coverage percentage
     */
    public function getCoveragePercentage(): float
    {
        $sheetArea = $this->width * $this->height;
        if ($sheetArea == 0) return 0;
        
        return round(($this->total_area / $sheetArea) * 100, 2);
    }

    /**
     * Calculate and set price based on dimensions and coverage
     */
    public function calculatePrice(): void
    {
        $pricing = DtfPricing::findByDimensions((float)$this->width, (float)$this->height);
        
        if (!$pricing) {
            // If no exact match, calculate proportional price
            $pricing = DtfPricing::where('is_active', true)->first();
            if ($pricing) {
                $area = (float)$this->width * (float)$this->height;
                $pricePerSquareInch = $pricing->base_price / ($pricing->width * $pricing->height);
                $this->price = round($area * $pricePerSquareInch, 2);
            }
            return;
        }

        $coveragePercentage = $this->getCoveragePercentage();
        $this->coverage_percentage = $coveragePercentage;
        $this->price = $pricing->calculatePrice($coveragePercentage);
    }

    /**
     * Submit for approval
     */
    public function submitForApproval(): bool
    {
        $this->submitted_at = now();
        $this->status = 'processing';
        $this->calculatePrice();
        return $this->save();
    }

    /**
     * Approve the gang sheet
     */
    public function approve(User $user, ?string $notes = null): bool
    {
        $this->approved_at = now();
        $this->approved_by = $user->id;
        $this->approval_notes = $notes;
        $this->status = 'completed';
        return $this->save();
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(string $paymentId, string $paymentMethod): bool
    {
        $this->payment_status = 'paid';
        $this->payment_id = $paymentId;
        $this->payment_method = $paymentMethod;
        $this->paid_at = now();
        $this->production_status = 'pending';
        return $this->save();
    }

    /**
     * Start production
     */
    public function startProduction(): bool
    {
        $this->production_status = 'in_production';
        $this->production_started_at = now();
        return $this->save();
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(?string $trackingNumber = null): bool
    {
        $this->production_status = 'completed';
        $this->completed_at = now();
        if ($trackingNumber) {
            $this->tracking_number = $trackingNumber;
        }
        return $this->save();
    }

    /**
     * Scope: pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'processing')
            ->whereNotNull('submitted_at')
            ->whereNull('approved_at');
    }

    /**
     * Scope: approved
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Scope: paid
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope: in production
     */
    public function scopeInProduction($query)
    {
        return $query->where('production_status', 'in_production');
    }

    /**
     * Check if can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft']) && 
               is_null($this->approved_at) && 
               $this->payment_status === 'pending';
    }

    /**
     * Check if requires payment
     */
    public function requiresPayment(): bool
    {
        return $this->payment_status === 'pending' && 
               !is_null($this->approved_at) &&
               $this->price > 0;
    }
}

