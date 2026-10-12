<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Historial;
use App\Models\User;

class HistorialController extends Controller
{
    public function index(Request $request)
    {
        $query = Historial::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $registros = $query->get();

        $usuarios = User::all();

        return view('Admin.historial', compact('registros', 'usuarios'));
    }
}