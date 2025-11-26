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
        Schema::create('product_subclasses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_class_id')->nullable()->constrained('product_classes');
            $table->string('name');
            $table->string('code')->unique()->index();
            $table->string('slug')->unique()->index();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_subclasses');
    }
};
