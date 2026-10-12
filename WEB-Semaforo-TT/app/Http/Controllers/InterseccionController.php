<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Interseccion;
use App\Models\Historial;

class InterseccionController extends Controller
{
    public function index()
    {
        $intersecciones = Interseccion::all();
        return view('Admin.mis_prototipos', compact('intersecciones'));
    }

    public function create()
    {
        $intersecciones = \App\Models\Interseccion::all(); 

        $baseIp = '192.168.1.';
        $ultimoSemaforo = Interseccion::latest('id')->first();

        if ($ultimoSemaforo) {
            $partes = explode('.', $ultimoSemaforo->ip_controlador);
            $ultimoOcteto = intval(end($partes)) + 1;
        } else {
            $ultimoOcteto = 50;
        }
        $ipSugerida = $baseIp . $ultimoOcteto;

        return view('Admin.crear_prototipo', compact('intersecciones', 'ipSugerida'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:interseccions,nombre',
            'ip_controlador' => 'required|ip|unique:interseccions,ip_controlador',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'latitud_destino' => 'required|numeric', 
            'longitud_destino' => 'required|numeric', 
            'tiempo_verde' => 'required|integer|min:15|max:90',
            'tiempo_amarillo' => 'required|integer|min:3|max:6',
            'tiempo_rojo' => 'required|integer|min:15|max:90',
            'estado_operativo' => 'required|string'
        ]);

        $interseccion = \App\Models\Interseccion::create([
            'nombre' => $request->nombre,
            'ip_controlador' => $request->ip_controlador,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'latitud_destino' => $request->latitud_destino, 
            'longitud_destino' => $request->longitud_destino, 
            'tiempo_verde' => $request->tiempo_verde,
            'tiempo_amarillo' => $request->tiempo_amarillo,
            'tiempo_rojo' => $request->tiempo_rojo,
            'estado_operativo' => $request->estado_operativo,
        ]);

        Historial::create([
            'user_id' => auth()->id(),
            'accion' => 'Creación de Prototipo',
            'detalles' => 'Se registró una nueva intersección: ' . $interseccion->nombre,
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('prototipos.index')->with('success', 'Prototipo registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $interseccion = Interseccion::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|unique:interseccions,nombre,'.$id,
            'ip_controlador' => 'required|ip|unique:interseccions,ip_controlador,'.$id,
            'tiempo_verde' => 'required|integer|min:15|max:90',
            'tiempo_amarillo' => 'required|integer|min:3|max:6',
            'tiempo_rojo' => 'required|integer|min:15|max:90',
            'latitud_destino' => 'nullable|numeric', 
            'longitud_destino' => 'nullable|numeric', 
            'estado_operativo' => 'required|string'
        ]);

        $interseccion->nombre = $request->nombre;
        $interseccion->ip_controlador = $request->ip_controlador;
        $interseccion->tiempo_verde = $request->tiempo_verde;
        $interseccion->tiempo_amarillo = $request->tiempo_amarillo;
        $interseccion->tiempo_rojo = $request->tiempo_rojo;
        $interseccion->estado_operativo = $request->estado_operativo;

        if ($request->has('latitud_destino') && $request->latitud_destino) {
            $interseccion->latitud_destino = $request->latitud_destino;
            $interseccion->longitud_destino = $request->longitud_destino;
        }

        $interseccion->save();

        Historial::create([
            'user_id' => auth()->id(),
            'accion' => 'Actualización de Prototipo',
            'detalles' => 'Se editaron los parámetros de la intersección: ' . $interseccion->nombre,
            'ip_address' => $request->ip()
        ]);

        \App\Models\RegistroCiclo::create([
            'interseccion_id' => $interseccion->id,
            'modo_operativo' => $request->estado_operativo,
            'tiempo_verde' => $request->tiempo_verde,
            'tiempo_amarillo' => $request->tiempo_amarillo,
            'tiempo_rojo' => $request->tiempo_rojo
        ]);

        return back()->with('success', 'Prototipo actualizado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $interseccion = Interseccion::findOrFail($id);
        $nombre = $interseccion->nombre;
        $interseccion->delete();

        Historial::create([
            'user_id' => auth()->id(),
            'accion' => 'Eliminación de Prototipo',
            'detalles' => 'Se eliminó la intersección: ' . $nombre,
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Prototipo eliminado correctamente del sistema.');
    }

   public function forzarEstado(Request $request, $id)
    {
        $request->validate([
            'modo_forzado' => 'required|string',
            'motivo' => 'required|string|min:5'
        ]);

        $interseccion = \App\Models\Interseccion::findOrFail($id);
        $interseccion->estado_operativo = $request->modo_forzado;
        $interseccion->save();

        $tipoAccion = in_array($request->modo_forzado, ['Inteligente', 'Fijo', 'Mantenimiento'])
                      ? 'Restauración de Modo Operativo'
                      : 'Estado Forzado Activado';

        Historial::create([
            'user_id' => auth()->id(),
            'accion' => $tipoAccion,
            'detalles' => 'Intersección: ' . $interseccion->nombre . ' | Nuevo Modo: ' . $request->modo_forzado . ' | Motivo: ' . $request->motivo,
            'ip_address' => $request->ip()
        ]);

        \App\Models\RegistroCiclo::create([
            'interseccion_id' => $interseccion->id,
            'modo_operativo' => $request->modo_forzado,
            'tiempo_verde' => $interseccion->tiempo_verde,
            'tiempo_amarillo' => $interseccion->tiempo_amarillo,
            'tiempo_rojo' => $interseccion->tiempo_rojo
        ]);

        return back()->with('success', 'Estado de la intersección actualizado correctamente.');
    }
}