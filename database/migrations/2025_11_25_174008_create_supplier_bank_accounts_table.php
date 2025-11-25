<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');

            $table->string('bank_name');
            $table->string('account_number')->nullable();
            $table->string('clabe', 18)->unique()->nullable();
            $table->string('swift_code')->nullable();
            $table->string('currency', 3)->default('MXN');

            $table->boolean('is_primary')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('supplier_id');
            $table->index('status');
            $table->index('is_primary');
            $table->index('clabe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_bank_accounts');
    }
};
