<?php

namespace App\Http\Controllers;

use App\Support\Opiniones;
use Illuminate\Http\Request;

class OpinionController extends Controller
{
    public function create()
    {
        return view('opiniones.create');
    }

    public function store(Request $request)
    {
        $usuario = auth()->user();
        $nombre = trim((string) $request->input('nombre', ''));
        $mensaje = trim((string) $request->input('mensaje', ''));

        if ($nombre === '' && $usuario) {
            $nombre = $usuario->nombre_completo;
        }

        if ($mensaje === '') {
            return back()->withInput()->with('error', 'Escribe tu comentario u observación antes de enviar.');
        }

        $origen = ($usuario && $usuario->esEstudiante()) ? 'Estudiante' : 'Visitante';

        if (Opiniones::guardar($origen, $nombre, $mensaje)) {
            return redirect()->route('opiniones.create')->with('enviado', true);
        }

        return back()->withInput()->with('error', 'No se pudo guardar tu opinión. Intenta de nuevo.');
    }
}