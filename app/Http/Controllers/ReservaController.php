<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Prestamo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    // "Mis reservas" del estudiante
    public function index()
    {
        $usuario = auth()->user();
        if (!$usuario || !$usuario->esEstudiante()) {
            return redirect()->route('catalogo');
        }

        Prestamo::marcarAtrasados();

        $prestamos = Prestamo::with('libro')
            ->where('id_usuario', $usuario->id_usuario)
            ->orderByDesc('id_prestamo')
            ->get();

        return view('catalogo.mis_reservas', compact('prestamos'));
    }

    // Días de plazo para devolver el libro (se calcula automáticamente)
    const DIAS_PRESTAMO = 7;

    // Reserva en línea (responde JSON, lo llama catalogo.js)
    // La fecha de préstamo SIEMPRE es la de hoy; el estudiante no la elige.
    public function store(Request $request)
    {
        $usuario = auth()->user();
        if (!$usuario || !$usuario->esEstudiante()) {
            return response()->json(['exito' => false, 'mensaje' => 'Debes iniciar sesión como estudiante para reservar un libro.']);
        }

        $id_libro = (int) $request->input('id_libro', 0);
        $observaciones = trim((string) $request->input('observaciones', ''));

        $hoy = Carbon::now('America/La_Paz');
        $fecha_prestamo = $hoy->toDateString();
        $fecha_devolucion = $hoy->copy()->addDays(self::DIAS_PRESTAMO)->toDateString();

        $duplicado = Prestamo::where('id_libro', $id_libro)
            ->where('id_usuario', $usuario->id_usuario)
            ->whereIn('estado', ['prestado', 'atrasado'])
            ->exists();

        if ($duplicado) {
            return response()->json(['exito' => false, 'mensaje' => 'Ya tienes un préstamo activo de este libro.']);
        }

        try {
            $libro = DB::transaction(function () use ($id_libro, $usuario, $fecha_prestamo, $fecha_devolucion, $observaciones) {
                // lockForUpdate evita que dos estudiantes tomen el último ejemplar a la vez
                $libro = Libro::where('id_libro', $id_libro)->where('estado', 'activo')->lockForUpdate()->first();

                if (!$libro || $libro->cantidad_disponible < 1) {
                    return null;
                }

                Prestamo::create([
                    'id_libro'                  => $libro->id_libro,
                    'id_usuario'                => $usuario->id_usuario,
                    'id_registrado_por'         => $usuario->id_usuario,
                    'origen'                    => 'estudiante',
                    'fecha_prestamo'            => $fecha_prestamo,
                    'fecha_devolucion_esperada' => $fecha_devolucion,
                    'observaciones'             => $observaciones,
                    'estado'                    => 'prestado',
                ]);
                $libro->decrement('cantidad_disponible');

                return $libro;
            });
        } catch (\Throwable $e) {
            return response()->json(['exito' => false, 'mensaje' => 'Ocurrió un error al registrar el préstamo.']);
        }

        if (!$libro) {
            return response()->json(['exito' => false, 'mensaje' => 'No hay ejemplares disponibles de este libro.']);
        }

        return response()->json([
            'exito'            => true,
            'mensaje'          => '¡Tu préstamo fue registrado con éxito!',
            'titulo'           => $libro->titulo,
            'fecha_prestamo'   => Carbon::parse($fecha_prestamo)->format('d/m/Y'),
            'fecha_devolucion' => Carbon::parse($fecha_devolucion)->format('d/m/Y'),
        ]);
    }
}
