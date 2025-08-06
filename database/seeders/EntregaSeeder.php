<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EntregaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('entregas')->insert([
            'cliente_id' => 1,
            'repartidor_id' => 1, // ID del admin creado en DatabaseSeeder
            'fecha_hora' => Carbon::now(),
            'estado' => 'pendiente',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
