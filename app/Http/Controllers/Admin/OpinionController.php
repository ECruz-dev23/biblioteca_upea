<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Opiniones;
use App\Support\SimplePDF;
use Illuminate\Http\Request;

class OpinionController extends Controller
{
    // Formulario + todas las opiniones recibidas
    public function index()
    {
        return view('admin.opiniones.index', ['todas' => Opiniones::obtener()]);
    }

    public function store(Request $request)
    {
        $mensaje = trim((string) $request->input('mensaje', ''));

        if ($mensaje === '') {
            return back()->withInput()->with('error', 'Escribe tu observación antes de enviar.');
        }

        if (Opiniones::guardar('Administrador', auth()->user()->nombre_completo, $mensaje)) {
            return redirect()->route('admin.opiniones.index')->with('enviado', true);
        }

        return back()->withInput()->with('error', 'No se pudo guardar tu opinión. Intenta de nuevo.');
    }

    // Reporte completo en PDF
    public function pdf()
    {
        $opiniones = Opiniones::obtener();

        $pdf = new SimplePDF();
        $pdf->titulo("Biblioteca Ciencias del Desarrollo - UPEA - Reporte de Opiniones y Sugerencias");
        $pdf->subtitulo("Carrera de Ciencias del Desarrollo - Generado: " . now()->format('d/m/Y H:i') . " - Total: " . count($opiniones));
        $pdf->espacio();

        if (count($opiniones) > 0) {
            foreach ($opiniones as $o) {
                $pdf->filaTabla(["[" . $o['fecha'] . "] " . $o['origen'] . " - " . $o['nombre']], [95]);
                $pdf->filaTabla([$o['mensaje']], [95]);
                $pdf->espacio();
            }
        } else {
            $pdf->filaTabla(["Aun no se ha recibido ninguna opinion."], [95]);
        }

        return $pdf->descargar("reporte_opiniones_sugerencias.pdf");
    }
}