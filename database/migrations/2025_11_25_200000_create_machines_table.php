<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('process_id')->nullable()->constrained('processes')->nullOnDelete();
            $table->string('speed_mh')->nullable();
            $table->string('speed_kgh')->nullable();
            $table->string('circumference_total')->nullable();
            $table->string('max_width')->nullable();
            $table->string('max_center')->nullable();
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
