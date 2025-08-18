<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Si actualmente es ENUM, ajusta la lista para incluir 'cliente'
        // (MariaDB/MySQL exige TODAS las opciones en el MODIFY)
        DB::statement("
            ALTER TABLE users
            MODIFY rol ENUM('admin','repartidor','cliente')
            NOT NULL DEFAULT 'cliente'
        ");
    }

    public function down(): void
    {
        // Vuelve al estado anterior si lo deseas (ejemplo: solo admin)
        DB::statement("
            ALTER TABLE users
            MODIFY rol ENUM('admin','repartidor')
            NOT NULL DEFAULT 'admin'
        ");
    }
};
