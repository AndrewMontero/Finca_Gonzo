<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->timestamp('fecha_hora')->nullable();
            $table->string('estado',30)->default('pendiente'); // pendiente|entregada|cancelada
            $table->timestamps();
            // Si tienes tabla clientes:
            // $table->foreign('cliente_id')->references('id')->on('clientes')->nullOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('entregas'); }
};
