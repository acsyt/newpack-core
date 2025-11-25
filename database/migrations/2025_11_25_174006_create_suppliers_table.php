<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('company_name');
            $table->string('contact_name')->nullable();
            $table->string('email')->unique()->nullable();

            $table->string('phone', 20)->nullable();
            $table->string('phone_secondary', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();

            $table->foreignId('suburb_id')->nullable()->constrained()->onDelete('set null');
            $table->string('street')->nullable();
            $table->string('exterior_number', 20)->nullable();
            $table->string('interior_number', 20)->nullable();
            $table->text('address_reference')->nullable();

            $table->string('rfc', 13)->unique()->nullable();
            $table->string('legal_name')->nullable();
            $table->string('tax_system', 10)->nullable();
            $table->string('use_cfdi', 10)->nullable()->default('G03');

            $table->enum('supplier_type', ['product', 'service', 'both'])->default('product');
            $table->string('payment_terms', 100)->nullable();
            $table->decimal('credit_limit', 15, 2)->nullable();

            $table->enum('status', ['active', 'inactive', 'suspended', 'blacklisted'])->default('active');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index('company_name');
            $table->index('contact_name');
            $table->index('status');
            $table->index('supplier_type');
            $table->index('email');
            $table->index('rfc');
            $table->index('suburb_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
