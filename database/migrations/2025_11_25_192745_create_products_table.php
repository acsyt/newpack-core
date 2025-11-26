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

            $table->string('type')->index();

            $table->foreignId('measure_unit_id')->constrained('measure_units');

            $table->decimal('average_cost', 12, 4)->default(0);
            $table->decimal('last_purchase_price', 12, 4)->nullable();

            $table->decimal('current_stock', 12, 4)->default(0); // Manejar como cache

            $table->decimal('min_stock', 12, 4)->default(0);
            $table->decimal('max_stock', 12, 4)->nullable();

            $table->boolean('track_batches')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sellable')->default(false);
            $table->boolean('is_purchasable')->default(false);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
