<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();

            $table->foreignId('source_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('destination_warehouse_id')->constrained('warehouses')->onDelete('cascade');

            $table->enum('status', ['pending', 'shipped', 'completed', 'cancelled'])->default('pending');

            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->foreignId('shipped_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->text('notes')->nullable();
            $table->text('receiving_notes')->nullable();

            $table->timestamps();

            // Índices para mejorar rendimiento
            $table->index(['status']);
            $table->index(['source_warehouse_id', 'destination_warehouse_id']);
            $table->index(['shipped_at']);
            $table->index(['received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
