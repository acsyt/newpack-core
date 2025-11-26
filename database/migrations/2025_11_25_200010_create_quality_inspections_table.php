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

            $table->decimal('quantity_inspected', 12, 4);
            $table->decimal('quantity_rejected', 12, 4)->default(0);
            $table->decimal('quantity_approved', 12, 4)->virtualAs('quantity_inspected - quantity_rejected');

            $table->json('defects')->nullable();
            $table->text('notes')->nullable();

            $table->enum('action_taken', ['scrap', 'rework', 'conditional_approval', 'quarantine'])->nullable();

            $table->timestamp('inspected_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_inspections');
    }
};
