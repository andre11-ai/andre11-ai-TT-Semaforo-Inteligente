<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interseccion extends Model
{
    use HasFactory;

    // AQUI ESTÁ LA SOLUCIÓN: Agregamos latitud_destino y longitud_destino
    protected $fillable = [
        'nombre',
        'ip_controlador',
        'latitud',
        'longitud',
        'latitud_destino',
        'longitud_destino',
        'tiempo_verde',
        'tiempo_amarillo',
        'tiempo_rojo',
        'estado_operativo',
        'estado_red'
    ];

    public function registrosCiclos()
    {
        return $this->hasMany(RegistroCiclo::class);
    }
}
