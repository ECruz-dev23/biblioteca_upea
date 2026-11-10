<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Autoregistro de estudiantes desde el catálogo público (JSON)
class RegistroController extends Controller
{
    public function store(Request $request)
    {
        $nombre = trim((string) $request->input('nombre_completo'));
        $usuario_login = trim((string) $request->input('usuario'));
        $correo = trim((string) $request->input('correo'));
        $ci = trim((string) $request->input('ci'));
        $registro = trim((string) $request->input('registro_universitario'));
        $carrera = trim((string) $request->input('carrera'));
        $telefono = trim((string) $request->input('telefono'));
        $semestre = trim((string) $request->input('semestre'));
        $curso = trim((string) $request->input('curso'));   
        $clave = (string) $request->input('contrasena');

        if ($carrera === '') {
            $carrera = 'Ciencias del Desarrollo';
        }

        if ($nombre === '' || $usuario_login === '' || $correo === '' || $ci === '' || $semestre === '' || $curso === '' || $clave === '') {
            return $this->error('Nombre, usuario, correo, CI, semestre, curso y contraseña son obligatorios.');
        }
        if (strlen($clave) < 6) {
            return $this->error('La contraseña debe tener al menos 6 caracteres.');
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $this->error('Correo electrónico no válido.');
        }
        if (preg_match('/\s/', $usuario_login)) {
            return $this->error('El nombre de usuario no puede tener espacios.');
        }
        if (Usuario::where('usuario', $usuario_login)->orWhere('correo', $correo)->exists()) {
            return $this->error('Ese usuario, correo o CI ya está registrado.');
        }

        try {
            $nuevo = Usuario::create([
                'nombre_completo'        => $nombre,
                'usuario'                => $usuario_login,
                'correo'                 => $correo,
                'contrasena'             => Hash::make($clave),
                'rol'                    => 'estudiante',
                'ci'                     => $ci,
                'registro_universitario' => $registro !== '' ? $registro : null,
                'carrera'                => $carrera,
                'telefono'               => $telefono !== '' ? $telefono : null,
                'semestre'               => $semestre,
                'curso'                  => $curso,
            ]);
        } catch (\Throwable $e) {
            return $this->error('Ocurrió un error al registrar la cuenta.');
        }

        Auth::login($nuevo);
        $request->session()->regenerate();
        $nuevo->update(['ultima_conexion' => now()]);

        return response()->json(['exito' => true, 'nombre' => $nombre]);
    }

    private function error(string $mensaje)
    {
        return response()->json(['exito' => false, 'mensaje' => $mensaje]);
    }
}