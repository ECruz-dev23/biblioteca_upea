<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    // Datos que se guardan igual para todos los roles
    private const CAMPOS_COMUNES = [
        'ci', 'registro_universitario', 'carrera', 'telefono', 'direccion',
        'nombre_referencia', 'telefono_referencia', 'direccion_referencia',
    ];

    // Junta los datos comunes y los que dependen del rol
   private function datosPorRol(Request $request, string $rol): array
   {
        $datos = $request->only(self::CAMPOS_COMUNES);
        $esAyudante = in_array($rol, ['pasante', 'becario']);
        $conCurso = in_array($rol, ['estudiante', 'pasante', 'becario']);

        $datos['materia']     = $rol === 'docente' ? $request->input('materia') : null;
        $datos['institucion'] = $esAyudante ? $request->input('institucion') : null;
        $datos['semestre']    = $conCurso ? $request->input('semestre') : null;
        $datos['curso']       = $conCurso ? $request->input('curso') : null;

        return $datos;
    }

    public function index(Request $request)
    {
        $busqueda = trim((string) $request->query('q', ''));
        $filtro_rol = (string) $request->query('rol', 'todos');

        $usuarios = Usuario::query()
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
            ->when($filtro_rol !== 'todos', fn ($q) => $q->where('rol', $filtro_rol))
            ->orderByDesc('fecha_registro')
            ->get();

        return view('admin.usuarios.index', compact('usuarios', 'busqueda', 'filtro_rol'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $obligatorios = 'Nombre, usuario, correo, contraseña y rol son obligatorios.';
        $duplicado = 'Error: el usuario o el correo ya están registrados, o hubo un problema al guardar los datos.';

        $request->validate([
            'nombre_completo' => ['required'],
            'usuario'         => ['required', 'regex:/^\S+$/', 'unique:usuarios,usuario'],
            'correo'          => ['required', 'email', 'unique:usuarios,correo'],
            'contrasena'      => ['required', 'min:6'],
            'rol'             => ['required', 'in:estudiante,docente,pasante,becario'],
            'ci'              => ['required'],
            'materia'         => ['required_if:rol,docente'],
            'semestre'        => ['required_if:rol,estudiante'],
            'curso'           => ['required_if:rol,estudiante'],
        ], [
            'nombre_completo.required' => $obligatorios,
            'usuario.required'         => $obligatorios,
            'correo.required'          => $obligatorios,
            'contrasena.required'      => $obligatorios,
            'rol.required'             => $obligatorios,
            'rol.in'                   => $obligatorios,
            'ci.required'              => 'El carnet de identidad (CI) es obligatorio.',
            'materia.required_if'      => 'La materia que imparte es obligatoria para docentes.',
            'semestre.required_if'     => 'El semestre es obligatorio para estudiantes.',
            'curso.required_if'        => 'El curso es obligatorio para estudiantes.',
            'contrasena.min'           => 'La contraseña debe tener al menos 6 caracteres.',
            'usuario.regex'            => 'El nombre de usuario no puede tener espacios.',
            'usuario.unique'           => $duplicado,
            'correo.unique'            => $duplicado,
            'correo.email'             => $duplicado,
        ]);

        $rol = $request->input('rol');

        try {
            Usuario::create(array_merge([
                'nombre_completo' => $request->input('nombre_completo'),
                'usuario'         => $request->input('usuario'),
                'correo'          => $request->input('correo'),
                'contrasena'      => Hash::make($request->input('contrasena')),
                'rol'             => $rol,
            ], $this->datosPorRol($request, $rol)));
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $duplicado);
        }

        return redirect()->route('admin.usuarios.index')->with('msg', 'Usuario registrado correctamente');
    }

    public function edit(Usuario $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $mensajeDup = 'Error al actualizar. Verifique que el usuario y el correo no estén duplicados.';

        $request->validate([
            'nombre_completo'  => ['required'],
            'usuario'          => ['required', 'regex:/^\S+$/', Rule::unique('usuarios', 'usuario')->ignore($usuario->id_usuario, 'id_usuario')],
            'correo'           => ['required', 'email', Rule::unique('usuarios', 'correo')->ignore($usuario->id_usuario, 'id_usuario')],
            'rol'              => ['required', 'in:estudiante,docente,pasante,becario,administrador'],
            'estado'           => ['required', 'in:activo,inactivo'],
            'nueva_contrasena' => ['nullable', 'min:6'],
            'ci'               => ['required_unless:rol,administrador'],
            'materia'          => ['required_if:rol,docente'],
            'semestre'        => ['required_if:rol,estudiante'],
            'curso'           => ['required_if:rol,estudiante'],
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'usuario.required'         => 'El usuario es obligatorio.',
            'correo.required'          => 'El correo es obligatorio.',
            'usuario.regex'            => 'El nombre de usuario no puede tener espacios.',
            'usuario.unique'           => $mensajeDup,
            'correo.unique'            => $mensajeDup,
            'correo.email'             => 'Correo electrónico no válido.',
            'nueva_contrasena.min'     => 'La contraseña debe tener al menos 6 caracteres.',
            'ci.required_unless'       => 'El carnet de identidad (CI) es obligatorio.',
            'materia.required_if'      => 'La materia que imparte es obligatoria para docentes.',
            'semestre.required_if'     => 'El semestre es obligatorio para estudiantes.',
            'curso.required_if'        => 'El curso es obligatorio para estudiantes.',
        ]);

        $rol = $request->input('rol');

        $datos = array_merge(
            $request->only(['nombre_completo', 'usuario', 'correo', 'rol', 'estado']),
            $this->datosPorRol($request, $rol)
        );

        if ($request->filled('nueva_contrasena')) {
            $datos['contrasena'] = Hash::make($request->input('nueva_contrasena'));
        }

        try {
            $usuario->update($datos);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $mensajeDup);
        }

        return redirect()->route('admin.usuarios.index')->with('msg', 'Usuario actualizado correctamente');
    }

        // Desactiva el usuario (nunca se borra: se conserva el historial)
    public function destroy(Usuario $usuario)
    {
        if ($usuario->id_usuario == auth()->id()) {
            return redirect()->route('admin.usuarios.index')->with('msg', 'No puedes desactivar tu propia cuenta');
        }

        $usuario->update(['estado' => 'inactivo']);

        return redirect()->route('admin.usuarios.index')
            ->with('msg', 'Usuario desactivado correctamente. Ya no podrá iniciar sesión, pero su historial se conserva.');
    }

    public function activar(Usuario $usuario)
    {
        $usuario->update(['estado' => 'activo']);

        return redirect()->route('admin.usuarios.index')->with('msg', 'Usuario reactivado correctamente');
    }
}