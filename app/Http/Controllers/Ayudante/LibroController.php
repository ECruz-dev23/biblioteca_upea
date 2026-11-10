<?php

namespace App\Http\Controllers\Ayudante;

use App\Http\Controllers\Controller;
use App\Models\Libro;
use Illuminate\Http\Request;

// Solo lectura: los ayudantes no pueden crear, editar ni eliminar libros
class LibroController extends Controller
{
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

        return view('ayudantes.libros.index', compact('libros', 'busqueda'));
    }
}