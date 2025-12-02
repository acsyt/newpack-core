<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->decimal('quantity_missing', 12, 4)->nullable()->after('quantity_received');
            $table->decimal('quantity_damaged', 12, 4)->nullable()->after('quantity_missing');
        });
    }

    public function down(): void
    {
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->dropColumn(['quantity_missing', 'quantity_damaged']);
        });
    }
};
