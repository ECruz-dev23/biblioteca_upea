<?php

namespace App\Http\Controllers\Ayudante;

use App\Http\Controllers\Controller;
use App\Support\Opiniones;
use Illuminate\Http\Request;

class OpinionController extends Controller
{
    public function create()
    {
        return view('ayudantes.opiniones.create');
    }

    public function store(Request $request)
    {
        $mensaje = trim((string) $request->input('mensaje', ''));

        if ($mensaje === '') {
            return back()->withInput()->with('error', 'Escribe tu observación antes de enviar.');
        }

        $usuario = auth()->user();
        $origen = ucfirst($usuario->rol); // Pasante o Becario

        if (Opiniones::guardar($origen, $usuario->nombre_completo, $mensaje)) {
            return redirect()->route('ayudantes.opiniones.create')->with('enviado', true);
        }

        return back()->withInput()->with('error', 'No se pudo guardar tu opinión. Intenta de nuevo.');
    }
}