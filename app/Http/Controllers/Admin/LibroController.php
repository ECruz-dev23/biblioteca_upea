<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LibroController extends Controller
{
    private function reglas(?Libro $libro = null): array
    {
        return [
            'codigo'           => ['required', 'max:30', Rule::unique('libros', 'codigo')->ignore($libro?->id_libro, 'id_libro')],
            'titulo'           => ['required', 'max:200'],
            'autor'            => ['required', 'max:150'],
            'editorial'        => ['nullable', 'max:100'],
            'anio_publicacion' => ['nullable', 'integer'],
            'id_categoria'     => ['nullable', 'exists:categorias,id_categoria'],
            'descripcion'      => ['nullable'],
            'cantidad_total'   => ['required', 'integer', 'min:1'],
            'ubicacion'        => ['nullable', 'max:50'],
        ];
    }

    private function mensajes(): array
    {
        $obligatorios = 'Los campos código, título y autor son obligatorios.';
        $invalido = 'Error: el código ya existe o los datos son inválidos.';

        return [
            'codigo.required' => $obligatorios,
            'titulo.required' => $obligatorios,
            'autor.required'  => $obligatorios,
            'codigo.unique'   => $invalido,
            'codigo.max'      => $invalido,
            'titulo.max'      => $invalido,
            'autor.max'       => $invalido,
            'editorial.max'   => $invalido,
            'ubicacion.max'   => $invalido,
            'anio_publicacion.integer' => $invalido,
            'cantidad_total.*' => $invalido,
            'id_categoria.exists' => $invalido,
        ];
    }

    public function index(Request $request)
    {
        $busqueda = trim((string) $request->query('q', ''));

        $libros = Libro::with('categoria')
            ->when($busqueda !== '', function ($q) use ($busqueda) {
                $like = "%$busqueda%";
                $q->where(function ($w) use ($like) {
                    $w->where('titulo', 'like', $like)
                      ->orWhere('autor', 'like', $like)
                      ->orWhere('codigo', 'like', $like);
                });
            })
            ->orderBy('titulo')
            ->get();

        return view('admin.libros.index', compact('libros', 'busqueda'));
    }

    public function create()
    {
        return view('admin.libros.create', [
            'libro'      => new Libro(['cantidad_total' => 1]),
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas(), $this->mensajes());
        $datos['cantidad_disponible'] = $datos['cantidad_total'];

        Libro::create($datos);

        return redirect()->route('admin.libros.index')->with('msg', 'Libro registrado correctamente');
    }

    public function edit(Libro $libro)
    {
        return view('admin.libros.edit', [
            'libro'      => $libro,
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Libro $libro)
    {
        $reglas = $this->reglas($libro);
        $reglas['estado'] = ['required', 'in:activo,inactivo'];

        $datos = $request->validate($reglas, $this->mensajes());

        // Se conserva la cantidad de ejemplares que están prestados
        $prestados = $libro->cantidad_total - $libro->cantidad_disponible;
        $datos['cantidad_disponible'] = max(0, $datos['cantidad_total'] - $prestados);

        $libro->update($datos);

        return redirect()->route('admin.libros.index')->with('msg', 'Libro actualizado correctamente');
    }

    // Desactiva el libro (nunca se borra: se conserva el historial)
    public function destroy(Libro $libro)
    {
        $libro->update(['estado' => 'inactivo']);

        return redirect()->route('admin.libros.index')
            ->with('msg', 'Libro desactivado correctamente. Ya no aparece en el catálogo, pero su historial se conserva.');
    }

    public function activar(Libro $libro)
    {
        $libro->update(['estado' => 'activo']);

        return redirect()->route('admin.libros.index')->with('msg', 'Libro reactivado correctamente');
    }
    
}