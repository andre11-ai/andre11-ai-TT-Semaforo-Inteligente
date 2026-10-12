<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlnDiccionario;
use App\Models\Historial;

class DiccionarioController extends Controller
{
    public function index()
    {
        $palabras = PlnDiccionario::latest()->get();
        return view('Admin.diccionario', compact('palabras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'palabra' => 'required|string|unique:pln_diccionarios,palabra',
            'categoria' => 'required|string',
            'peso' => 'required|numeric|min:1|max:10'
        ]);

        $palabra = PlnDiccionario::create([
            'palabra' => strtolower($request->palabra),
            'categoria' => $request->categoria,
            'peso' => $request->peso
        ]);

        Historial::create([
            'user_id' => auth()->id(),
            'accion' => 'Nueva Palabra PLN',
            'detalles' => 'Se agregó al diccionario: "' . $palabra->palabra . '" con peso ' . $palabra->peso,
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Palabra clave agregada exitosamente para la IA.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'palabra' => 'required|string|unique:pln_diccionarios,palabra,'.$id,
            'categoria' => 'required|string',
            'peso' => 'required|numeric|min:1|max:10'
        ]);

        $palabra = PlnDiccionario::findOrFail($id);
        $palabra->palabra = strtolower($request->palabra);
        $palabra->categoria = $request->categoria;
        $palabra->peso = $request->peso;
        $palabra->save();

        Historial::create([
            'user_id' => auth()->id(),
            'accion' => 'Actualización Palabra PLN',
            'detalles' => 'Se editó la palabra: "' . $palabra->palabra . '" con nuevo peso ' . $palabra->peso,
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Palabra actualizada correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $palabra = PlnDiccionario::findOrFail($id);
        $nombrePalabra = $palabra->palabra;
        $palabra->delete();

        Historial::create([
            'user_id' => auth()->id(),
            'accion' => 'Eliminación Palabra PLN',
            'detalles' => 'Se eliminó del diccionario la palabra: "' . $nombrePalabra . '"',
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Palabra eliminada correctamente.');
    }
}