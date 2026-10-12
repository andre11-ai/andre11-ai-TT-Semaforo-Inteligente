<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'André Eduardo Ayala Sánchez',
            'email' => 'Aayalas1900@alumno.ipn.mx',
            'password' => Hash::make('Admin1234'),
            'rol' => 'Administrador'
        ]);

        User::create([
            'name' => 'Operador de Tráfico',
            'email' => 'operador@alumno.ipn.mx',
            'password' => Hash::make('Usuario1234'),
            'rol' => 'Usuario'
        ]);
    }
}