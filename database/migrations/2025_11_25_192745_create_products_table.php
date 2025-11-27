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
            $table->string('slug')->unique();
            $table->string('sku')->unique();

            $table->string('type')->index();

            $table->foreignId('measure_unit_id')->constrained('measure_units');
            $table->foreignId('product_class_id')->nullable()->constrained('product_classes');
            $table->foreignId('product_subclass_id')->nullable()->constrained('product_subclasses');

            $table->decimal('average_cost', 12, 4)->default(0);
            $table->decimal('last_purchase_price', 12, 4)->nullable();

            $table->decimal('current_stock', 12, 4)->default(0); // Manejar como cache

            $table->decimal('min_stock', 12, 4)->default(0);
            $table->decimal('max_stock', 12, 4)->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_sellable')->default(false);
            $table->boolean('is_purchasable')->default(false);

            // Specifications
            $table->decimal('width', 12, 4)->nullable();
            $table->decimal('width_min', 12, 4)->nullable();
            $table->decimal('width_max', 12, 4)->nullable();

            $table->decimal('gusset', 12, 4)->nullable();
            $table->decimal('gusset_min', 12, 4)->nullable();
            $table->decimal('gusset_max', 12, 4)->nullable();

            $table->decimal('length', 12, 4)->nullable();
            $table->decimal('length_min', 12, 4)->nullable();
            $table->decimal('length_max', 12, 4)->nullable();

            $table->decimal('gauge', 12, 4)->nullable();
            $table->decimal('gauge_min', 12, 4)->nullable();
            $table->decimal('gauge_max', 12, 4)->nullable();

            $table->decimal('nominal_weight', 12, 6)->nullable();
            $table->decimal('weight_min', 12, 6)->nullable();
            $table->decimal('weight_max', 12, 6)->nullable();

            $table->string('resin_type')->nullable();
            $table->string('color')->nullable();
            $table->string('additive')->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
