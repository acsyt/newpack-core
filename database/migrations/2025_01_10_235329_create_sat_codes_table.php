<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sat_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->string('description')->nullable();
            $table->string('include_transfer_vat',50)->nullable();
            $table->string('include_transfer_ieps',50)->nullable();
            $table->boolean('border_strip_incentive')->default(false);
            $table->text('similar')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        $sql = file_get_contents(database_path('sql/sat_codes.sql'));
        // DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sat_codes');
    }
};
