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
            $table->foreignId('product_id')->constrained('products');

            $table->enum('type', [
                'purchase_entry',
                'production_output',
                'production_consumption',
                'sales_shipment',
                'adjustment',
                'transfer'
            ]);

            $table->decimal('quantity', 12, 4);

            $table->nullableMorphs('reference');

            $table->string('batch_code')->nullable();
            $table->string('location')->nullable();

            $table->decimal('balance_after', 12, 4);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
