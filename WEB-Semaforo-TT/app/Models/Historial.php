<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    protected $fillable = ['user_id', 'accion', 'detalles', 'ip_address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}