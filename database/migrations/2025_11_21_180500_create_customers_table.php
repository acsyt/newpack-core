<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('last_name');
            $table->string('email')->unique()->nullable();

            $table->string('phone', 20)->nullable();
            $table->string('phone_secondary', 20)->nullable();

            $table->foreignId('suburb_id')->nullable()->constrained()->onDelete('set null');
            $table->string('street')->nullable();
            $table->string('exterior_number', 20)->nullable();
            $table->string('interior_number', 20)->nullable();
            $table->text('address_reference')->nullable();

            $table->string('rfc', 13)->unique()->nullable();
            $table->string('legal_name')->nullable(); // Razón Social
            $table->string('tax_system', 10)->nullable(); // Regimen Fiscal
            $table->string('cfdi_use', 10)->nullable()->default('G03'); // Uso CFDI

            $table->enum('status', ['active', 'inactive', 'suspended', 'blacklisted'])->default('active');
            $table->enum('client_type', ['individual', 'company'])->default('individual');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('client_type');
            $table->index('email');
            $table->index('rfc');
            $table->index('suburb_id');
            $table->index('legal_name');
            $table->index(['last_name', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
