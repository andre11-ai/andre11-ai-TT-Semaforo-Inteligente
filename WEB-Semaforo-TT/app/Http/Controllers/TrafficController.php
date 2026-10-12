<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Interseccion;
use App\Models\RegistroCiclo;

class TrafficController extends Controller
{
    public function index()
    {
        $intersecciones = Interseccion::all();
        return view('Admin.monitoreo_global', compact('intersecciones'));
    }

    public function detalle($id)
    {
        $interseccion = Interseccion::findOrFail($id);

        $latDestino = (float) $interseccion->latitud_destino;
        $lonDestino = (float) $interseccion->longitud_destino;
        
        if ($latDestino == 0.0 || $lonDestino == 0.0) {
            $latDestino = (float) $interseccion->latitud + 0.0045; // Simulamos 500m
            $lonDestino = (float) $interseccion->longitud;
        }

        try {
            $respuesta = Http::timeout(5)->post('http://127.0.0.1:8000/api/pln/procesar', [
                'interseccion_id' => str_pad($interseccion->id, 3, '0', STR_PAD_LEFT),
                'lat_semaforo' => (float) $interseccion->latitud,
                'lon_semaforo' => (float) $interseccion->longitud,
                'lat_destino' => $latDestino,
                'lon_destino' => $lonDestino,
                'calle_principal' => $interseccion->nombre
            ]);

            if ($respuesta->successful()) {
                $datosApi = $respuesta->json();

                if (strtolower($interseccion->estado_operativo) === 'inteligente') {
                    RegistroCiclo::create([
                        'interseccion_id' => $interseccion->id,
                        'modo_operativo' => 'Inteligente',
                        'tiempo_verde' => $datosApi['tiempos_calculados']['fase_verde_segundos'],
                        'tiempo_amarillo' => 4,
                        'tiempo_rojo' => $datosApi['tiempos_calculados']['fase_roja_segundos']
                    ]);
                }
            } else {
                $datosApi = null;
            }
        } catch (\Exception $e) {
            $datosApi = null;
        }

        return view('Admin.monitoreo', compact('interseccion', 'datosApi'));
    }

    public function monitor($id)
    {
        return $this->detalle($id); 
    }

   public function fetchLiveData($id)
    {
        $interseccion = Interseccion::findOrFail($id);

        if (strtolower($interseccion->estado_operativo) !== 'inteligente') {
            return response()->json(['status' => 'offline', 'mensaje' => 'Modo Inteligente inactivo']);
        }

        $latDestino = (float) $interseccion->latitud_destino;
        $lonDestino = (float) $interseccion->longitud_destino;
        
        if ($latDestino == 0.0 || $lonDestino == 0.0) {
            $latDestino = (float) $interseccion->latitud + 0.0045;
            $lonDestino = (float) $interseccion->longitud;
        }

        try {
            $respuesta = \Illuminate\Support\Facades\Http::timeout(5)->post('http://127.0.0.1:8000/api/pln/procesar', [
                'interseccion_id' => str_pad($interseccion->id, 3, '0', STR_PAD_LEFT),
                'lat_semaforo' => (float) $interseccion->latitud,
                'lon_semaforo' => (float) $interseccion->longitud,
                'lat_destino' => $latDestino,
                'lon_destino' => $lonDestino,
                'calle_principal' => $interseccion->nombre
            ]);

            if ($respuesta->successful()) {
                $datosApi = $respuesta->json();

                RegistroCiclo::create([
                    'interseccion_id' => $interseccion->id,
                    'modo_operativo' => 'Inteligente',
                    'tiempo_verde' => $datosApi['tiempos_calculados']['fase_verde_segundos'],
                    'tiempo_amarillo' => 4,
                    'tiempo_rojo' => $datosApi['tiempos_calculados']['fase_roja_segundos']
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => $datosApi
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }

        return response()->json(['status' => 'error']);
    }

    public function pingPython()
    {
        try {
            \Illuminate\Support\Facades\Http::timeout(2)->get('http://127.0.0.1:8000/');
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }
}