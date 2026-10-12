<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroCiclo extends Model
{
    use HasFactory;

    protected $fillable = [
        'interseccion_id',
        'modo_operativo',
        'tiempo_verde',
        'tiempo_amarillo',
        'tiempo_rojo'
    ];

    public function interseccion()
    {
        return $this->belongsTo(Interseccion::class, 'interseccion_id');
    }
}
