<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroCiclo;
use App\Models\Interseccion;

class RegistroCicloController extends Controller
{
    public function index(Request $request)
    {
        $query = RegistroCiclo::with('interseccion')->orderBy('created_at', 'desc');

        if ($request->filled('interseccion_id')) {
            $query->where('interseccion_id', $request->interseccion_id);
        }

        if ($request->filled('modo')) {
            $query->where('modo_operativo', $request->modo);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        if ($request->filled('hora')) {
            $horaStr = str_pad($request->hora, 2, '0', STR_PAD_LEFT);
            $query->whereTime('created_at', 'like', $horaStr . ':%');
        }

        $registros = $query->paginate(50);

        $intersecciones = Interseccion::all();

        return view('Admin.historial_ciclos', compact('registros', 'intersecciones'));
    }
}
