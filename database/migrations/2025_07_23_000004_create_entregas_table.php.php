<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('repartidor_id')->nullable(); // ✅ debe ser nullable
            $table->dateTime('fecha_hora');
            $table->string('estado');
            $table->timestamps();

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->onDelete('cascade');

            $table->foreign('repartidor_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null'); // ✅ ahora sí funcionará
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
