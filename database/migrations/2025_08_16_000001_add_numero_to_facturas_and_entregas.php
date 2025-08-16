<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->unsignedBigInteger('numero')->after('id')->nullable()->unique();
        });

        Schema::table('entregas', function (Blueprint $table) {
            $table->unsignedBigInteger('numero')->after('id')->nullable()->unique();
        });

        // Backfill seguro del consecutivo basado en created_at
        \App\Models\Factura::orderBy('created_at')
            ->get()
            ->each(function ($f, $i) { $f->update(['numero' => $i + 1]); });

        \App\Models\Entrega::orderBy('created_at')
            ->get()
            ->each(function ($e, $i) { $e->update(['numero' => $i + 1]); });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn('numero');
        });
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropColumn('numero');
        });
    }
};
