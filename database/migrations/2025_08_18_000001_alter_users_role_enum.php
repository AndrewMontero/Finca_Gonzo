<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Para MySQL/MariaDB: convertimos 'rol' en ENUM con valores válidos y default 'cliente'
        DB::statement("ALTER TABLE users 
            MODIFY rol ENUM('admin','repartidor','cliente') NOT NULL DEFAULT 'cliente'
        ");
    }

    public function down(): void
    {
        // Vuelve a string (ajústalo si tu estado anterior era distinto)
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol', 20)->default('cliente')->change();
        });
    }
};
