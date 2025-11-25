<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\ProductType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();

            // 'wip' = Work In Progress (Semielaborado, ej: el rollo antes de hacerse bolsa)
            $table->enum('type', array_column(ProductType::cases(), 'value'));

            $table->string('unit_of_measure', 10); // kg, lt, pza

            // PRECIO vs COSTO
            // Costo Promedio (para valorar inventario)
            $table->decimal('average_cost', 12, 4)->default(0);
            // Último precio de compra (para referencia de compras)
            $table->decimal('last_purchase_price', 12, 4)->nullable();

            $table->decimal('current_stock', 12, 4)->default(0); // Manejar como cache

            $table->decimal('min_stock', 12, 4)->default(0);
            $table->decimal('max_stock', 12, 4)->nullable();

            // Si es true, obligas a capturar # de Lote al recibir y al consumir.
            $table->boolean('track_batches')->default(false);

            $table->boolean('is_active')->default(true); // Para "borrado lógico"
            $table->boolean('is_sellable')->default(false); // MP no se vende, Compuesto sí
            $table->boolean('is_purchasable')->default(false); // MP se compra, Compuesto no (se fabrica)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
