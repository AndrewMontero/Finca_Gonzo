<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('repartidor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('fecha_hora');
            $table->enum('estado', ['pendiente', 'realizada', 'cancelada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('entregas');
    }
};
