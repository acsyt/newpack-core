<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificamos la columna status para incluir 'requested' y eliminar 'pending'
        // Usamos DB::statement porque Doctrine DBAL tiene problemas con ENUMs a veces
        DB::statement("ALTER TABLE transfers MODIFY COLUMN status ENUM('requested', 'shipped', 'completed', 'cancelled') NOT NULL DEFAULT 'requested'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertimos al estado anterior
        DB::statement("ALTER TABLE transfers MODIFY COLUMN status ENUM('pending', 'shipped', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
