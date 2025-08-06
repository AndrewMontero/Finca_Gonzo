<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('productos')->insert([
            'nombre' => 'Producto de prueba',
            'unidad_medida' => 'unidad',
            'precio_unitario' => 1000.00,
            'stock_minimo' => 10,
            'stock_maximo' => 100,
            'stock_actual' => 50,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
