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
        // Paso 1: Eliminar la restricción de clave foránea temporalmente
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropForeign(['repartidor_id']); // Eliminar FK constraint
        });

        // Paso 2: Verificar si hay entregas sin repartidor
        $entregasSinRepartidor = DB::table('entregas')->whereNull('repartidor_id')->count();

        if ($entregasSinRepartidor > 0) {
            // Asignar repartidor por defecto
            $repartidorPorDefecto = DB::table('users')->where('rol', 'repartidor')->first();

            if ($repartidorPorDefecto) {
                DB::table('entregas')
                    ->whereNull('repartidor_id')
                    ->update(['repartidor_id' => $repartidorPorDefecto->id]);
            }
        }

        // Paso 3: Cambiar la columna a NOT NULL
        Schema::table('entregas', function (Blueprint $table) {
            $table->unsignedBigInteger('repartidor_id')->nullable(false)->change();
        });

        // Paso 4: Restaurar la restricción de clave foránea
        Schema::table('entregas', function (Blueprint $table) {
            $table->foreign('repartidor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar FK constraint
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropForeign(['repartidor_id']);
        });

        // Revertir la columna a nullable
        Schema::table('entregas', function (Blueprint $table) {
            $table->unsignedBigInteger('repartidor_id')->nullable(true)->change();
        });

        // Restaurar FK constraint
        Schema::table('entregas', function (Blueprint $table) {
            $table->foreign('repartidor_id')->references('id')->on('users');
        });
    }
};
