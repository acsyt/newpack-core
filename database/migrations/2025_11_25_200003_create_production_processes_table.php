<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained('production_orders')->onDelete('cascade');
            $table->string('stage_name'); // Extrusion, Printing, etc.
            $table->integer('sequence_order')->default(1);
            $table->enum('status', ['pending', 'in_progress', 'paused', 'finished'])->default('pending');

            $table->foreignId('machine_id')->nullable()->constrained('machines')->onDelete('set null');
            $table->foreignId('operator_id')->nullable()->constrained('users');

            $table->decimal('input_quantity', 12, 4)->default(0);
            $table->decimal('output_quantity', 12, 4)->default(0);
            $table->decimal('waste_quantity', 12, 4)->default(0);

            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_processes');
    }
};
