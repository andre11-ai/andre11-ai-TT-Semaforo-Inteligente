<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InterseccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ahora = Carbon::now();

        $semaforos = [
            [
                "nombre" => "AV INSURGENTES CENTRO - AV. RIBERA DE SAN COSME (1)",
                "latitud" => 19.440513,
                "longitud" => -99.157670,
                "latitud_destino" => 19.439828,
                "longitud_destino" => -99.155911,
            ],
            [
                "nombre" => "AV INSURGENTES CENTRO - AV. RIBERA DE SAN COSME (2)",
                "latitud" => 19.439013,
                "longitud" => -99.153579,
                "latitud_destino" => 19.439782,
                "longitud_destino" => -99.155459,
            ],
            [
                "nombre" => "AV INSURGENTES CENTRO - AV. RIBERA DE SAN COSME (3)",
                "latitud" => 19.438291,
                "longitud" => -99.156267,
                "latitud_destino" => 19.439603,
                "longitud_destino" => -99.155704,
            ],
            [
                "nombre" => "AV INSURGENTES CENTRO - AV. RIBERA DE SAN COSME (4)",
                "latitud" => 19.441390,
                "longitud" => -99.155057,
                "latitud_destino" => 19.440011,
                "longitud_destino" => -99.155667,
            ],
            [
                "nombre" => "AV INSURGENTES CENTRO - TOMAS ALVA EDISON (1)",
                "latitud" => 19.436718,
                "longitud" => -99.156949,
                "latitud_destino" => 19.438014,
                "longitud_destino" => -99.156387,
            ],
            [
                "nombre" => "AV INSURGENTES CENTRO - TOMAS ALVA EDISON (2)",
                "latitud" => 19.439663,
                "longitud" => -99.155848,
                "latitud_destino" => 19.438327,
                "longitud_destino" => -99.156429,
            ],
            [
                "nombre" => "AV INSURGENTES CENTRO - TOMAS ALVA EDISON (3)",
                "latitud" => 19.438497,
                "longitud" => -99.157297,
                "latitud_destino" => 19.438216,
                "longitud_destino" => -99.156597,
            ],
            [
                "nombre" => "PONCIANO ARRIAGA Y TOMAS ALVA EDISON (1)",
                "latitud" => 19.436746,
                "longitud" => -99.154064,
                "latitud_destino" => 19.437284,
                "longitud_destino" => -99.153872,
            ],
            [
                "nombre" => "PONCIANO ARRIAGA Y TOMAS ALVA EDISON (2)",
                "latitud" => 19.438661,
                "longitud" => -99.153490,
                "latitud_destino" => 19.437511,
                "longitud_destino" => -99.153920,
            ],
            [
                "nombre" => "PONCIANO ARRIAGA Y TOMAS ALVA EDISON (3)",
                "latitud" => 19.437746,
                "longitud" => -99.155103,
                "latitud_destino" => 19.437446,
                "longitud_destino" => -99.154095,
            ],
            [
                "nombre" => "BUENAVISTA Y AVENIDA MEXICO TENOCHTITLAN (1)",
                "latitud" => 19.437763,
                "longitud" => -99.153700,
                "latitud_destino" => 19.438595,
                "longitud_destino" => -99.153368,
            ],
            [
                "nombre" => "BUENAVISTA Y AVENIDA MEXICO TENOCHTITLAN (2)",
                "latitud" => 19.440221,
                "longitud" => -99.152876,
                "latitud_destino" => 19.439128,
                "longitud_destino" => -99.153335,
            ],
            [
                "nombre" => "BUENAVISTA Y AVENIDA MEXICO TENOCHTITLAN (3)",
                "latitud" => 19.439540,
                "longitud" => -99.155214,
                "latitud_destino" => 19.438842,
                "longitud_destino" => -99.153576,
            ],
            [
                "nombre" => "BUENAVISTA Y AVENIDA MEXICO TENOCHTITLAN (4)",
                "latitud" => 19.438335,
                "longitud" => -99.150695,
                "latitud_destino" => 19.438854,
                "longitud_destino" => -99.153098,
            ],
            [
                "nombre" => "BUENAVISTA (JESUS GARCIA) Y MINA (1)",
                "latitud" => 19.439052,
                "longitud" => -99.153188,
                "latitud_destino" => 19.440697,
                "longitud_destino" => -99.152546,
            ],
            [
                "nombre" => "BUENAVISTA (JESUS GARCIA) Y MINA (2)",
                "latitud" => 19.442214,
                "longitud" => -99.152544,
                "latitud_destino" => 19.441017,
                "longitud_destino" => -99.152642,
            ],
            [
                "nombre" => "BUENAVISTA (JESUS GARCIA) Y MINA (3)",
                "latitud" => 19.440604,
                "longitud" => -99.151693,
                "latitud_destino" => 19.440804,
                "longitud_destino" => -99.152391,
            ],
            [
                "nombre" => "C. GUERRERO Y AV. HIDALGO (1)",
                "latitud" => 19.439691,
                "longitud" => -99.148802,
                "latitud_destino" => 19.438150,
                "longitud_destino" => -99.149071,
            ],
            [
                "nombre" => "C. GUERRERO Y AV. HIDALGO (2)",
                "latitud" => 19.437628,
                "longitud" => -99.147332,
                "latitud_destino" => 19.437912,
                "longitud_destino" => -99.148813,
            ],
            [
                "nombre" => "C. GUERRERO Y AV. HIDALGO (3)",
                "latitud" => 19.438114,
                "longitud" => -99.150375,
                "latitud_destino" => 19.437854,
                "longitud_destino" => -99.149308,
            ]
        ];

        $registrosFinales = [];
        $ipBase = 50; 

        foreach ($semaforos as $index => $s) {
            $registrosFinales[] = [
                'nombre' => $s['nombre'],
                'ip_controlador' => '192.168.1.' . ($ipBase + $index),
                'latitud' => $s['latitud'],
                'longitud' => $s['longitud'],
                'latitud_destino' => $s['latitud_destino'],
                'longitud_destino' => $s['longitud_destino'],
                'tiempo_verde' => 45,
                'tiempo_amarillo' => 4,
                'tiempo_rojo' => 45,
                'estado_operativo' => 'Inteligente',
                'created_at' => $ahora,
                'updated_at' => $ahora
            ];
        }

        DB::table('interseccions')->insert($registrosFinales);
    }
}
