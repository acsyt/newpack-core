<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->foreignId('country_id')->constrained();
            $table->timestamps();
        });

        DB::table('states')->insert([
            ['id' => 1, 'name' => 'Aguascalientes', 'code' => 'AS', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Baja California', 'code' => 'BC', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Baja California Sur', 'code' => 'BS', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Campeche', 'code' => 'CC', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Coahuila', 'code' => 'CL', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Colima', 'code' => 'CM', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Chiapas', 'code' => 'CS', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Chihuahua', 'code' => 'CH', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => 'Ciudad de México', 'code' => 'DF', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Durango', 'code' => 'DG', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'Guanajuato', 'code' => 'GT', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => 'Guerrero', 'code' => 'GR', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => 'Hidalgo', 'code' => 'HG', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'name' => 'Jalisco', 'code' => 'JC', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'name' => 'México', 'code' => 'MC', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'name' => 'Michoacán', 'code' => 'MN', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'name' => 'Morelos', 'code' => 'MS', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'name' => 'Nayarit', 'code' => 'NT', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'name' => 'Nuevo León', 'code' => 'NL', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'name' => 'Oaxaca', 'code' => 'OC', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'name' => 'Puebla', 'code' => 'PL', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'name' => 'Querétaro', 'code' => 'QO', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'name' => 'Quintana Roo', 'code' => 'QR', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'name' => 'San Luis Potosí', 'code' => 'SP', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'name' => 'Sinaloa', 'code' => 'SL', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'name' => 'Sonora', 'code' => 'SR', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'name' => 'Tabasco', 'code' => 'TC', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'name' => 'Tamaulipas', 'code' => 'TS', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'name' => 'Tlaxcala', 'code' => 'TL', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'name' => 'Veracruz', 'code' => 'VZ', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'name' => 'Yucatán', 'code' => 'YN', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'name' => 'Zacatecas', 'code' => 'ZS', 'country_id' => '1', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
}
