<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Enums\ProductType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measure_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        DB::table('measure_units')->insert([
            ['name' => 'Kilogramo',      'code' => 'kg', 'description' => 'Unidad de masa', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Litro',          'code' => 'lt', 'description' => 'Unidad de volumen', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pieza',          'code' => 'pza', 'description' => 'Unidad de conteo', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metro',          'code' => 'm', 'description' => 'Unidad de longitud', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metro Cuadrado', 'code' => 'm2', 'description' => 'Unidad de área', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metro Cúbico',   'code' => 'm3', 'description' => 'Unidad de volumen', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('measure_units');
    }
};
