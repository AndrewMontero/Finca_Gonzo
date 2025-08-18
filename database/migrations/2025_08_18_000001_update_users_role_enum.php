<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Si tu motor no soporta change() sobre enum, cambia a string(20) con default('cliente')
            // Opción A (ENUM):
            $table->enum('rol', ['admin','repartidor','cliente'])->default('cliente')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['admin','repartidor'])->default('repartidor')->change();
        });
    }
};
