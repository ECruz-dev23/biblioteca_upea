<?php

namespace App\Http\Controllers\Ayudante;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// Los ayudantes administran solo estudiantes y docentes (no personal, no eliminan)
class UsuarioController extends Controller
{
    private const ROLES = ['estudiante', 'docente'];

    public function index(Request $request)
    {
        $busqueda = trim((string) $request->query('q', ''));
        $filtro_rol = (string) $request->query('rol', 'todos');

        $usuarios = Usuario::whereIn('rol', self::ROLES)
            ->when($busqueda !== '', function ($q) use ($busqueda) {
                $like = "%$busqueda%";
                $q->where(function ($w) use ($like) {
                    $w->where('nombre_completo', 'like', $like)
                      ->orWhere('usuario', 'like', $like)
                      ->orWhere('correo', 'like', $like)
                      ->orWhere('ci', 'like', $like)
                      ->orWhere('registro_universitario', 'like', $like);
                });
            })
            ->when(in_array($filtro_rol, self::ROLES), fn ($q) => $q->where('rol', $filtro_rol))
            ->orderByDesc('fecha_registro')
            ->get();

        return view('ayudantes.usuarios.index', compact('usuarios', 'busqueda', 'filtro_rol'));
    }

    public function create()
    {
        return view('ayudantes.usuarios.create');
    }

    public function store(Request $request)
    {
        $obligatorios = 'Nombre, usuario, correo, CI, contraseña y rol son obligatorios.';
        $duplicado = 'Error: el usuario o el correo ya están registrados, o hubo un problema al guardar los datos.';

        $request->validate([
            'nombre_completo' => ['required'],
            'usuario'         => ['required', 'regex:/^\S+$/', 'unique:usuarios,usuario'],
            'correo'          => ['required', 'email', 'unique:usuarios,correo'],
            'ci'              => ['required'],
            'contrasena'      => ['required', 'min:6'],
            'rol'             => ['required', 'in:estudiante,docente'],
            'materia'         => ['required_if:rol,docente'],
        ], [
            'nombre_completo.required' => $obligatorios,
            'usuario.required'         => $obligatorios,
            'correo.required'          => $obligatorios,
            'ci.required'              => $obligatorios,
            'contrasena.required'      => $obligatorios,
            'rol.required'             => $obligatorios,
            'rol.in'                   => $obligatorios,
            'materia.required_if'      => 'La materia que imparte es obligatoria para docentes.',
            'contrasena.min'           => 'La contraseña debe tener al menos 6 caracteres.',
            'usuario.regex'            => 'El nombre de usuario no puede tener espacios.',
            'usuario.unique'           => $duplicado,
            'correo.unique'            => $duplicado,
            'correo.email'             => $duplicado,
        ]);

        try {
            Usuario::create([
                'nombre_completo'        => $request->input('nombre_completo'),
                'usuario'                => $request->input('usuario'),
                'correo'                 => $request->input('correo'),
                'contrasena'             => Hash::make($request->input('contrasena')),
                'rol'                    => $request->input('rol'),
                'ci'                     => $request->input('ci'),
                'registro_universitario' => $request->input('registro_universitario'),
                'carrera'                => $request->input('carrera'),
                'telefono'               => $request->input('telefono'),
                'materia'                => $request->input('rol') === 'docente' ? $request->input('materia') : null,
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $duplicado);
        }

        return redirect()->route('ayudantes.usuarios.index')->with('msg', 'Usuario registrado correctamente');
    }

    public function edit(Usuario $usuario)
    {
        abort_unless(in_array($usuario->rol, self::ROLES), 404);

        return view('ayudantes.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        abort_unless(in_array($usuario->rol, self::ROLES), 404);

        $mensajeDup = 'Error al actualizar. Verifique que el usuario y el correo no estén duplicados.';

        $request->validate([
            'nombre_completo'  => ['required'],
            'usuario'          => ['required', 'regex:/^\S+$/', Rule::unique('usuarios', 'usuario')->ignore($usuario->id_usuario, 'id_usuario')],
            'correo'           => ['required', 'email', Rule::unique('usuarios', 'correo')->ignore($usuario->id_usuario, 'id_usuario')],
            'ci'               => ['required'],
            'rol'              => ['required', 'in:estudiante,docente'],
            'estado'           => ['required', 'in:activo,inactivo'],
            'nueva_contrasena' => ['nullable', 'min:6'],
            'materia'          => ['required_if:rol,docente'],
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'usuario.required'         => 'El usuario es obligatorio.',
            'correo.required'          => 'El correo es obligatorio.',
            'ci.required'              => 'El CI es obligatorio.',
            'usuario.regex'            => 'El nombre de usuario no puede tener espacios.',
            'usuario.unique'           => $mensajeDup,
            'correo.unique'            => $mensajeDup,
            'correo.email'             => 'Correo electrónico no válido.',
            'nueva_contrasena.min'     => 'La contraseña debe tener al menos 6 caracteres.',
            'materia.required_if'      => 'La materia que imparte es obligatoria para docentes.',
        ]);

        $datos = $request->only([
            'nombre_completo', 'usuario', 'correo', 'ci', 'registro_universitario',
            'carrera', 'telefono', 'rol', 'estado',
        ]);
        $datos['materia'] = $request->input('rol') === 'docente' ? $request->input('materia') : null;

        if ($request->filled('nueva_contrasena')) {
            $datos['contrasena'] = Hash::make($request->input('nueva_contrasena'));
        }

        try {
            $usuario->update($datos);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $mensajeDup);
        }

        return redirect()->route('ayudantes.usuarios.index')->with('msg', 'Usuario actualizado correctamente');
    }
}