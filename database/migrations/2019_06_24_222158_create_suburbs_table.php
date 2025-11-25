<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSuburbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suburbs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('zip_code_id')->constrained();
            $table->timestamps();
        });
        $sql = file_get_contents(database_path('sql/suburbs.sql'));
        DB::unprepared($sql);

        DB::statement("
            UPDATE suburbs
            SET name = IFNULL(
                CONVERT(
                    CAST(
                        CONVERT(name USING latin1)
                    AS BINARY)
                USING utf8mb4),
                name
            );
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suburbs');
    }
}
