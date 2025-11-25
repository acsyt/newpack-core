<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->timestamps();
        });

        DB::table('countries')->insert(['name' => 'México', 'code' => 'MX', 'created_at' => now(), 'updated_at' => now()]);

        $countries = config('countries');

        foreach ($countries as $countryCode => $countryName) {
            if ($countryCode !== 'MX') {
                DB::table('countries')->insert(['name' => $countryName, 'code' => $countryCode, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
