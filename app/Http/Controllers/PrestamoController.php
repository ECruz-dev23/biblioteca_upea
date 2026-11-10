<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Prestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestamoController extends Controller
{
    // 'admin' o 'ayudantes' según la ruta desde la que se llama
    private function panel(): string
    {
        return request()->routeIs('admin.*') ? 'admin' : 'ayudantes';
    }

    public function index(Request $request)
    {
        Prestamo::marcarAtrasados();

        $filtro = (string) $request->query('estado', 'todos');

        $prestamos = Prestamo::with(['libro', 'usuario'])
            ->when($filtro !== 'todos', fn ($q) => $q->where('estado', $filtro))
            ->orderByDesc('id_prestamo')
            ->get();

        return view('prestamos.index', [
            'prestamos' => $prestamos,
            'filtro'    => $filtro,
            'panel'     => $this->panel(),
        ]);
    }

    public function create()
    {
        $libros = Libro::where('cantidad_disponible', '>', 0)
            ->where('estado', 'activo')
            ->orderBy('titulo')
            ->get();

        return view('prestamos.create', [
            'libros' => $libros,
            'panel'  => $this->panel(),
        ]);
    }

    public function store(Request $request)
    {
        if (!$request->filled('id_libro') || !$request->filled('nombre_prestatario') || !$request->filled('ci_prestatario')) {
            return back()->withInput()->with('error', 'Selecciona un libro y completa al menos el nombre y CI del prestatario.');
        }

        $libro = Libro::find($request->input('id_libro'));
        $fecha_prestamo = $request->input('fecha_prestamo');
        $fecha_devolucion = $request->input('fecha_devolucion_esperada');

        if (!$libro || $libro->cantidad_disponible < 1) {
            return back()->withInput()->with('error', 'No hay ejemplares disponibles de este libro.');
        }

        if (strtotime($fecha_devolucion) <= strtotime($fecha_prestamo)) {
            return back()->withInput()->with('error', 'La fecha de devolución esperada debe ser posterior a la fecha de préstamo.');
        }

        try {
            DB::transaction(function () use ($request, $libro, $fecha_prestamo, $fecha_devolucion) {
                Prestamo::create([
                    'id_libro'                  => $libro->id_libro,
                    'id_usuario'                => null,
                    'id_registrado_por'         => auth()->id(),
                    'origen'                    => 'presencial',
                    'nombre_prestatario'        => trim($request->input('nombre_prestatario')),
                    'ci_prestatario'            => trim($request->input('ci_prestatario')),
                    'carrera_prestatario'       => $request->input('carrera_prestatario'),
                    'telefono_prestatario'      => $request->input('telefono_prestatario'),
                    'fecha_prestamo'            => $fecha_prestamo,
                    'fecha_devolucion_esperada' => $fecha_devolucion,
                    'observaciones'             => $request->input('observaciones'),
                    'estado'                    => 'prestado',
                ]);
                $libro->decrement('cantidad_disponible');
            });
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Ocurrió un error al registrar el préstamo.');
        }

        return redirect()->route($this->panel() . '.prestamos.index')
            ->with('msg', 'Préstamo registrado correctamente');
    }

    // Marca un préstamo como devuelto y repone el ejemplar
    public function devolver(Prestamo $prestamo)
    {
        $volver = redirect()->route($this->panel() . '.prestamos.index');

        if ($prestamo->estado === 'devuelto') {
            return $volver->with('msg', 'Este préstamo ya fue devuelto');
        }

        try {
            DB::transaction(function () use ($prestamo) {
                $prestamo->update([
                    'estado'                => 'devuelto',
                    'fecha_devolucion_real' => now()->toDateString(),
                ]);
                Libro::where('id_libro', $prestamo->id_libro)->increment('cantidad_disponible');
            });
        } catch (\Throwable $e) {
            return $volver->with('msg', 'Error al registrar la devolución');
        }

        return $volver->with('msg', 'Devolución registrada correctamente');
    }
}