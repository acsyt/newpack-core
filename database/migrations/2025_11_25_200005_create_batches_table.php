<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('batch_code')->unique();
            $table->date('production_date')->nullable();
            $table->date('expiration_date')->nullable();

                // Origin tracking
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('production_order_id')->nullable()->constrained()->onDelete('set null');

            $table->decimal('initial_quantity', 12, 4);
            $table->decimal('current_quantity', 12, 4)->default(0);

            $table->json('quality_certificate')->nullable();
            $table->text('notes')->nullable();

            $table->enum('status', ['active', 'quarantine', 'expired', 'depleted', 'blocked'])->default('active');

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index('batch_code');
            $table->index('product_id');
            $table->index('status');
            $table->index('expiration_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
