<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlnDiccionario;

class PlnDiccionarioSeeder extends Seeder
{
    public function run(): void
    {
        $palabras = [
            ['lema' => 'choque', 'peso_gravedad' => 0.80, 'categoria' => 'Colisión'],
            ['lema' => 'accidente', 'peso_gravedad' => 0.85, 'categoria' => 'Colisión'],
            ['lema' => 'cerrar', 'peso_gravedad' => 1.00, 'categoria' => 'Bloqueo total'],
            ['lema' => 'bloqueo', 'peso_gravedad' => 1.00, 'categoria' => 'Bloqueo total'],
            ['lema' => 'manifestación', 'peso_gravedad' => 0.90, 'categoria' => 'Bloqueo social'],
            ['lema' => 'inundación', 'peso_gravedad' => 0.95, 'categoria' => 'Meteorológico'],
        ];

        foreach ($palabras as $palabra) {
            PlnDiccionario::create($palabra);
        }
    }
}