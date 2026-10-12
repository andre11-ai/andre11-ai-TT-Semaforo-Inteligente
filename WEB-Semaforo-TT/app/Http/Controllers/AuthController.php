<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email|ends_with:@alumno.ipn.mx',
            'password' => 'required|min:8|regex:/[A-Z]/|regex:/[0-9]/|confirmed'
        ]);

        $nuevoUsuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'Usuario'
        ]);

        \App\Models\Historial::create([
            'user_id' => $nuevoUsuario->id,
            'accion' => 'Auto-Registro de Cuenta',
            'detalles' => 'El operador se registró a sí mismo en la plataforma.',
            'ip_address' => $request->ip()
        ]);

        return redirect('/')->with('success', 'Cuenta creada. Inicia sesión.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            \App\Models\Historial::create([
                'user_id' => auth()->id(),
                'accion' => 'Inicio de Sesión',
                'detalles' => 'El usuario accedió al sistema con éxito.',
                'ip_address' => $request->ip()
            ]);

            if (Auth::user()->rol === 'Administrador') {
                return redirect()->intended('/Admin/dashboard');
            }
            return redirect()->intended('/Usuario/dashboard');
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            \App\Models\Historial::create([
                'user_id' => auth()->id(),
                'accion' => 'Cierre de Sesión',
                'detalles' => 'El usuario salió del sistema.',
                'ip_address' => $request->ip()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
