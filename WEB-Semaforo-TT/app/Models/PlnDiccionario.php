<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlnDiccionario extends Model
{
    use HasFactory;

    protected $fillable = [
        'palabra', 
        'categoria', 
        'peso' 
    ];
}