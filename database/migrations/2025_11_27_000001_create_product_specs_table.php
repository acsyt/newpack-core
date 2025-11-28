<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_specs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->unique()
                ->constrained('products')
                ->onDelete('cascade');

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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_specs');
    }
};

