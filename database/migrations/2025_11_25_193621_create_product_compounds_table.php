<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_compounds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('compound_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('products')->onDelete('restrict');

            $table->decimal('quantity', 12, 6);

            $table->decimal('wastage_percent', 5, 2)->default(0);

            $table->string('process_stage')->nullable()->index()->comment('Ej: EXTRUSION, IMPRESION, EMPAQUE');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['compound_id', 'ingredient_id', 'process_stage'], 'unique_recipe_line');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_compounds');
    }
};
