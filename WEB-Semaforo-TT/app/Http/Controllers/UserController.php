<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::all();
        return view('Admin.usuarios', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|ends_with:@alumno.ipn.mx',
            'password' => 'required|min:8|regex:/[A-Z]/|regex:/[0-9]/',
            'rol' => 'required|in:Administrador,Usuario'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol
        ]);

        \App\Models\Historial::create([
            'user_id' => auth()->id(),
            'accion' => 'Nueva Cuenta Registrada',
            'detalles' => 'Se dio de alta un ' . $request->rol . ' con el correo: ' . $request->email,
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Usuario registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$usuario->id,
            'rol' => 'required|in:Administrador,Usuario'
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->rol = $request->rol;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        \App\Models\Historial::create([
            'user_id' => auth()->id(),
            'accion' => 'Actualización de Cuenta',
            'detalles' => 'Se actualizaron los datos del operador: ' . $usuario->name,
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        $nombre = $usuario->name;
        $usuario->delete();

        \App\Models\Historial::create([
            'user_id' => auth()->id(),
            'accion' => 'Eliminación de Cuenta',
            'detalles' => 'Se dio de baja del sistema al operador: ' . $nombre,
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}
