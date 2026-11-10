<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // Login del personal (admin, pasantes, becari@s) en página completa
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $usuario_login = trim((string) $request->input('usuario'));
        $contrasena = (string) $request->input('contrasena');

        $usuario = Usuario::where('usuario', $usuario_login)->first();

        if (!$usuario) {
            return back()->with('error', 'No existe un usuario con ese nombre de usuario.');
        }
        if ($usuario->estado !== 'activo') {
            return back()->with('error', 'Este usuario se encuentra inactivo.');
        }
        if (!$usuario->esStaff()) {
            return back()->with('error', 'Esta cuenta es de estudiante. Usa el botón de iniciar sesión del catálogo público.');
        }
        if (!Hash::check($contrasena, $usuario->contrasena)) {
            return back()->with('error', 'Contraseña incorrecta.');
        }

        $this->iniciarSesion($request, $usuario);

        return redirect($usuario->rutaPanel());
    }

    // Login del modal del catálogo público (JSON)
    public function ajax(Request $request)
    {
        $usuario_login = trim((string) $request->input('usuario'));
        $contrasena = (string) $request->input('contrasena');

        if ($usuario_login === '' || $contrasena === '') {
            return response()->json(['exito' => false, 'mensaje' => 'Completa usuario y contraseña.']);
        }

        $usuario = Usuario::where('usuario', $usuario_login)->first();

        if (!$usuario) {
            return response()->json(['exito' => false, 'mensaje' => 'No existe una cuenta con ese usuario.']);
        }
        if ($usuario->estado !== 'activo') {
            return response()->json(['exito' => false, 'mensaje' => 'Esta cuenta está inactiva.']);
        }
        if (!Hash::check($contrasena, $usuario->contrasena)) {
            return response()->json(['exito' => false, 'mensaje' => 'Contraseña incorrecta.']);
        }

        $this->iniciarSesion($request, $usuario);

        return response()->json([
            'exito'     => true,
            'nombre'    => $usuario->nombre_completo,
            'rol'       => $usuario->rol,
            'redirigir' => $usuario->rutaPanel(),
        ]);
    }

    public function logout(Request $request)
    {
        $eraStaff = Auth::check() && Auth::user()->esStaff();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($eraStaff ? route('login') : route('catalogo'));
    }

    private function iniciarSesion(Request $request, Usuario $usuario): void
    {
        Auth::login($usuario);
        $request->session()->regenerate();
        $usuario->update(['ultima_conexion' => now()]);
    }
}