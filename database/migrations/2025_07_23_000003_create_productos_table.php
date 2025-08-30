<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('unidad_medida');
            $table->decimal('precio_unitario', 10, 2);
            $table->integer('stock_minimo');
            $table->integer('stock_maximo');
            $table->integer('stock_actual')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('productos');
    }
};
