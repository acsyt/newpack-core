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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            [
                'slug'          => 'iva',
                'value'         => '0.16',
                'description'   => 'IVA',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'slug'          => 'exchange_rate',
                'value'         => '1',
                'description'   => 'Tasa de cambio',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
