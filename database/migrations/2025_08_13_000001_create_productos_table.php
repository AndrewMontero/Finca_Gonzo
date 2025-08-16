<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',150);
            $table->string('unidad',30)->nullable();      // kg, caja, etc
            $table->decimal('precio',10,2)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('productos'); }
};
