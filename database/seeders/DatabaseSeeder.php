<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario Admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => bcrypt('123456'),
            'rol' => 'admin'
        ]);

        // Llamar otros seeders
        $this->call([
            ClienteSeeder::class,
            ProductoSeeder::class,
            EntregaSeeder::class,
            DetalleEntregaSeeder::class,
        ]);
    }
}
