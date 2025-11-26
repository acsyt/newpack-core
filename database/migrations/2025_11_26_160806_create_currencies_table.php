<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 3)->unique();
            $table->string('symbol', 10);
            $table->boolean('is_default')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        DB::table('currencies')->insert([
            [
                'name'          => 'Mexican Peso',
                'code'          => 'MXN',
                'symbol'        => '$',
                'is_default'    => true,
                'active'        => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'US Dollar',
                'code'          => 'USD',
                'symbol'        => 'USD $',
                'is_default'    => false,
                'active'        => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Euro',
                'code'          => 'EUR',
                'symbol'        => '€',
                'is_default'    => false,
                'active'        => true,
                'created_at'    => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Canadian Dollar',
                'code' => 'CAD',
                'symbol' => 'CAD $',
                'is_default' => false,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
