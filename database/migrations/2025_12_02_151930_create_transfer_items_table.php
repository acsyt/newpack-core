<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained('transfers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->foreignId('warehouse_location_source_id')->nullable()->constrained('warehouse_locations')->onDelete('set null');
            $table->foreignId('warehouse_location_destination_id')->nullable()->constrained('warehouse_locations')->onDelete('set null');

            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');

            $table->decimal('quantity_sent', 12, 4);
            $table->decimal('quantity_received', 12, 4)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            // Índices para mejorar rendimiento
            $table->index(['transfer_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_items');
    }
};
