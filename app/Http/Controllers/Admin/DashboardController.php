<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;

class DashboardController extends Controller
{
    public function index()
    {
        Prestamo::marcarAtrasados();

        $total_libros = Libro::where('estado', 'activo')->count();
        $total_ejemplares = Libro::where('estado', 'activo')->sum('cantidad_disponible');
        $total_estudiantes = Usuario::where('rol', 'estudiante')->where('estado', 'activo')->count();
        $total_prestamos_activos = Prestamo::whereIn('estado', ['prestado', 'atrasado'])->count();
        $total_atrasados = Prestamo::where('estado', 'atrasado')->count();

        $ultimos = Prestamo::with(['libro', 'usuario'])->orderByDesc('id_prestamo')->take(5)->get();

        return view('admin.dashboard', compact(
            'total_libros', 'total_ejemplares', 'total_estudiantes',
            'total_prestamos_activos', 'total_atrasados', 'ultimos'
        ));
    }
}