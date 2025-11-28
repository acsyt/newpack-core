<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('warehouse_location_id')->nullable()->constrained('warehouse_locations')->onDelete('set null');

            $table->enum('type', [
                'purchase_entry',
                'production_output',
                'production_consumption',
                'sales_shipment',
                'adjustment',
                'transfer'
            ]);

            $table->decimal('quantity', 12, 4);
            $table->decimal('balance_after', 12, 4);

            $table->nullableMorphs('reference');

            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->text('notes')->nullable();

            $table->timestamps();

            // Índices para mejorar el rendimiento
            $table->index(['product_id', 'warehouse_id']);
            $table->index(['created_at']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
