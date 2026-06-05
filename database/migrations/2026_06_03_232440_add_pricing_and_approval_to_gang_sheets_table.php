<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gang_sheets', function (Blueprint $table) {
            // Pricing
            $table->decimal('price', 10, 2)->nullable()->after('image_count');
            $table->decimal('coverage_percentage', 5, 2)->nullable()->after('price');
            
            // Approval workflow
            $table->boolean('requires_approval')->default(true)->after('status');
            $table->timestamp('submitted_at')->nullable()->after('requires_approval');
            $table->timestamp('approved_at')->nullable()->after('submitted_at');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('approved_at');
            $table->text('approval_notes')->nullable()->after('approved_by');
            
            // Payment
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('approval_notes');
            $table->string('payment_id')->nullable()->after('payment_status');
            $table->string('payment_method')->nullable()->after('payment_id');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
            
            // Production
            $table->enum('production_status', ['pending', 'in_production', 'completed', 'shipped'])->default('pending')->after('paid_at');
            $table->timestamp('production_started_at')->nullable()->after('production_status');
            $table->timestamp('completed_at')->nullable()->after('production_started_at');
            $table->string('tracking_number')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gang_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'price',
                'coverage_percentage',
                'requires_approval',
                'submitted_at',
                'approved_at',
                'approved_by',
                'approval_notes',
                'payment_status',
                'payment_id',
                'payment_method',
                'paid_at',
                'production_status',
                'production_started_at',
                'completed_at',
                'tracking_number',
            ]);
        });
    }
};
