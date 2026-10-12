<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiccionarioController;
use App\Http\Controllers\InterseccionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\TrafficController;
use App\Http\Controllers\RegistroCicloController;


Route::get('/', function () {
    return view('Login');
})->name('Login');

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {

    
    // Dashboard 
    Route::get('/Usuario/dashboard', function () {
        if(auth()->user()->rol !== 'Usuario') {
            abort(403, 'Acceso no autorizado.');
        }
        return view('Usuario.dashboard');
    })->name('usuario.dashboard');

    // Monitor y Forzar Estado
    Route::get('/monitor', [TrafficController::class, 'index'])->name('monitor.global');
    Route::get('/monitor/detalle/{id}', [TrafficController::class, 'detalle'])->name('monitor.detalle');
    Route::get('/prototipos/monitor/{id}', [TrafficController::class, 'monitor'])->name('prototipos.monitor');
    Route::post('/prototipos/{id}/forzar', [InterseccionController::class, 'forzarEstado'])->name('prototipos.forzar');

    // APIs
    Route::get('/api/monitor/datos/{id}', [TrafficController::class, 'fetchLiveData'])->name('api.monitor.datos');
    Route::get('/api/ping-python', [TrafficController::class, 'pingPython'])->name('api.ping.python');


    Route::middleware(['can_admin'])->group(function () {

        Route::get('/Admin/dashboard', function () {
            return view('Admin.dashboard');
        })->name('admin.dashboard');

        // Prototipos
        Route::get('/Admin/prototipos', [InterseccionController::class, 'index'])->name('prototipos.index');
        Route::get('/Admin/prototipos/crear', [InterseccionController::class, 'create'])->name('prototipos.create');
        Route::post('/Admin/prototipos/guardar', [InterseccionController::class, 'store'])->name('prototipos.store');
        Route::put('/prototipos/{id}', [InterseccionController::class, 'update'])->name('prototipos.update');
        Route::delete('/prototipos/{id}', [InterseccionController::class, 'destroy'])->name('prototipos.destroy');

        // Diccionario
        Route::get('/diccionario', [DiccionarioController::class, 'index'])->name('diccionario.index');
        Route::post('/diccionario', [DiccionarioController::class, 'store'])->name('diccionario.store');
        Route::put('/diccionario/{id}', [DiccionarioController::class, 'update'])->name('diccionario.update');
        Route::delete('/diccionario/{id}', [DiccionarioController::class, 'destroy'])->name('diccionario.destroy');

        // Usuarios
        Route::get('/Admin/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UserController::class, 'destroy'])->name('usuarios.destroy');

        // Historiales
        Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');
        Route::get('/historial-ciclos', [RegistroCicloController::class, 'index'])->name('historial.ciclos');

    });
});