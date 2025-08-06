<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('clientes')->insert([
            'nombre' => 'Cliente de prueba',
            'telefono' => '88888888',
            'correo' => 'cliente@example.com',
            'ubicacion' => 'San José, Costa Rica',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
