<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained('production_orders')->onDelete('cascade');
            $table->foreignId('production_process_id')->nullable()->constrained('production_processes')->onDelete('set null');

            $table->foreignId('inspector_id')->constrained('users');

            $table->enum('result', ['passed', 'failed', 'conditional']);
            $table->json('defects')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('inspected_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_inspections');
    }
};
