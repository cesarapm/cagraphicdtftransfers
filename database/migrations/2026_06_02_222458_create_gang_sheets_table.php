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
        Schema::create('gang_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->decimal('width', 8, 2); // inches
            $table->decimal('height', 8, 2); // inches
            $table->integer('dpi')->default(300);
            $table->json('images_data'); // Store positions and sizes
            $table->string('preview_path')->nullable(); // Preview image
            $table->string('final_path')->nullable(); // Final high-res image
            $table->decimal('total_area', 10, 2)->nullable(); // Total area in sq inches
            $table->integer('image_count')->default(0);
            $table->enum('status', ['draft', 'processing', 'completed', 'failed'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gang_sheets');
    }
};
