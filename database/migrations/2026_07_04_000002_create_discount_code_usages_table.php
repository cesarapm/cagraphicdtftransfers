<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_code_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_code_id')->constrained('discount_codes')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->timestamp('used_at');
            $table->timestamps();
            
            $table->unique(['discount_code_id', 'customer_id']); // Un cliente solo puede usar 1 vez por código
            $table->index(['discount_code_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_code_usages');
    }
};
