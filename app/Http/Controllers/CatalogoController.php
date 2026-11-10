<?php

namespace App\Http\Controllers;

use App\Models\BusquedaLog;
use App\Models\Categoria;
use App\Models\Libro;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $categorias_lista = Categoria::orderBy('nombre')->get();

        $busqueda = trim((string) $request->query('q', ''));
        $categoria_id = (string) $request->query('categoria', '');
        $vista = (string) $request->query('vista', 'todos');
        $titulo_seccion = 'Catálogo General';

        $query = Libro::with('categoria')->where('estado', 'activo');

        if ($busqueda !== '') {
            BusquedaLog::create(['termino' => $busqueda]);

            $like = "%$busqueda%";
            $query->where(function ($q) use ($like) {
                $q->where('titulo', 'like', $like)
                  ->orWhere('autor', 'like', $like)
                  ->orWhere('codigo', 'like', $like)
                  ->orWhere('editorial', 'like', $like);
            })->orderBy('titulo');
            $titulo_seccion = 'Resultados para: "' . $busqueda . '"';

        } elseif ($vista === 'mas_buscados') {
            $query->where('contador_vistas', '>', 0)->orderByDesc('contador_vistas')->limit(12);
            $titulo_seccion = 'Libros Más Buscados';

        } elseif ($categoria_id !== '') {
            $query->where('id_categoria', $categoria_id)->orderBy('titulo');
            $cat = $categorias_lista->firstWhere('id_categoria', (int) $categoria_id);
            if ($cat) {
                $titulo_seccion = 'Categoría: ' . $cat->nombre;
            }

        } else {
            $query->orderBy('titulo');
        }

        $libros = $query->get();

        return view('catalogo.index', compact(
            'libros', 'categorias_lista', 'busqueda', 'categoria_id', 'vista', 'titulo_seccion'
        ));
    }

    // Devuelve el detalle de un libro en JSON (lo usa el modal) y suma una vista
    public function detalle($id)
    {
        $libro = Libro::with('categoria')
            ->where('id_libro', $id)
            ->where('estado', 'activo')
            ->first();

        if (!$libro) {
            return response()->json(['exito' => false, 'mensaje' => 'Libro no encontrado.']);
        }

        $libro->increment('contador_vistas');

        $datos = array_merge($libro->attributesToArray(), [
            'categoria' => $libro->categoria->nombre ?? null,
        ]);

        $usuario = auth()->user();

        return response()->json([
            'exito'         => true,
            'libro'         => $datos,
            'sesion_activa' => (bool) $usuario,
            'es_estudiante' => $usuario ? $usuario->esEstudiante() : false,
        ]);
    }
}