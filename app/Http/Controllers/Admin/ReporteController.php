<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use App\Support\SimplePDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        Prestamo::marcarAtrasados();

        $mas_prestados = Libro::withCount('prestamos as veces')
            ->has('prestamos')->orderByDesc('veces')->take(10)->get();

        $mas_buscados = Libro::where('contador_vistas', '>', 0)
            ->orderByDesc('contador_vistas')->take(10)->get();

        $atrasados = Prestamo::with(['libro', 'usuario'])
            ->where('estado', 'atrasado')
            ->orderBy('fecha_devolucion_esperada')->get();

        $bajo_stock = Libro::where('cantidad_disponible', 0)->where('estado', 'activo')->get();

        $por_categoria = Categoria::withCount('libros as total')
            ->has('libros')->orderByDesc('total')->get();

        $usuarios_activos = Usuario::where('rol', 'estudiante')
            ->withCount('prestamos as total_prestamos')
            ->has('prestamos')->orderByDesc('total_prestamos')->take(10)->get();

        $reg_por_dia  = $this->registrosPor('dia', 10);
        $reg_por_mes  = $this->registrosPor('mes', 12);
        $reg_por_anio = $this->registrosPor('anio', 10);

        return view('admin.reportes.index', compact(
            'mas_prestados', 'mas_buscados', 'atrasados', 'bajo_stock', 'por_categoria', 'usuarios_activos',
            'reg_por_dia', 'reg_por_mes', 'reg_por_anio'
        ));
    }

    public function pdf(Request $request, string $tipo)
    {
        $pdf = new SimplePDF();
        $generado = now()->format('d/m/Y H:i');

        switch ($tipo) {
            case 'mas_buscados':
                $filas = Libro::where('contador_vistas', '>', 0)->orderByDesc('contador_vistas')->take(30)->get();
                $pdf->titulo("Biblioteca Ciencias del Desarrollo - UPEA - Reporte de Libros Mas Buscados");
                $pdf->subtitulo("Carrera de Ciencias del Desarrollo - Generado: " . $generado);
                $pdf->espacio();
                $anchos = [12, 45, 30, 10];
                $pdf->encabezadoTabla(["Codigo", "Titulo", "Autor", "Vistas"], $anchos);
                if ($filas->count() > 0) {
                    foreach ($filas as $f) {
                        $pdf->filaTabla([$f->codigo, $f->titulo, $f->autor, $f->contador_vistas], $anchos);
                    }
                } else {
                    $pdf->filaTabla(["Sin datos registrados aun.", "", "", ""], $anchos);
                }
                return $pdf->descargar("reporte_libros_mas_buscados.pdf");

            case 'mas_prestados':
                $filas = Libro::withCount('prestamos as veces')->has('prestamos')->orderByDesc('veces')->take(30)->get();
                $pdf->titulo("Biblioteca Ciencias del Desarrollo - UPEA - Reporte de Libros Mas Reservados");
                $pdf->subtitulo("Carrera de Ciencias del Desarrollo - Generado: " . $generado);
                $pdf->espacio();
                $anchos = [12, 45, 30, 10];
                $pdf->encabezadoTabla(["Codigo", "Titulo", "Autor", "Veces"], $anchos);
                if ($filas->count() > 0) {
                    foreach ($filas as $f) {
                        $pdf->filaTabla([$f->codigo, $f->titulo, $f->autor, $f->veces], $anchos);
                    }
                } else {
                    $pdf->filaTabla(["Sin datos registrados aun.", "", "", ""], $anchos);
                }
                return $pdf->descargar("reporte_libros_mas_reservados.pdf");

            case 'usuarios_registrados':
                $periodo = in_array($request->query('periodo'), ['dia', 'mes', 'anio']) ? $request->query('periodo') : 'dia';
                $titulos = ['dia' => 'Por Dia', 'mes' => 'Por Mes', 'anio' => 'Por Anio'];
                $etiquetas = ['dia' => 'Fecha (AAAA-MM-DD)', 'mes' => 'Mes (AAAA-MM)', 'anio' => 'Anio (AAAA)'];
                $filas = $this->registrosPor($periodo);
                $pdf->titulo("Biblioteca Ciencias del Desarrollo - UPEA");
                $pdf->subtitulo("Reporte de Usuarios Registrados (" . $titulos[$periodo] . ") - Generado: " . $generado);
                $pdf->espacio();
                $anchos = [40, 20];
                $pdf->encabezadoTabla([$etiquetas[$periodo], "Registrados"], $anchos);
                if ($filas->count() > 0) {
                    foreach ($filas as $f) {
                        $pdf->filaTabla([$f->fecha_grupo, $f->total], $anchos);
                    }
                } else {
                    $pdf->filaTabla(["Sin registros en este periodo.", "0"], $anchos);
                }
                return $pdf->descargar("reporte_usuarios_registrados_" . $periodo . ".pdf");

            case 'usuarios_activos':
                $filas = Usuario::where('rol', 'estudiante')->withCount('prestamos as total_prestamos')
                    ->has('prestamos')->orderByDesc('total_prestamos')->take(30)->get();
                $pdf->titulo("Biblioteca Ciencias del Desarrollo - UPEA - Reporte de Usuarios Mas Activos");
                $pdf->subtitulo("Carrera de Ciencias del Desarrollo - Generado: " . $generado);
                $pdf->espacio();
                $anchos = [30, 12, 30, 10];
                $pdf->encabezadoTabla(["Nombre", "CI", "Correo", "Total"], $anchos);
                if ($filas->count() > 0) {
                    foreach ($filas as $f) {
                        $pdf->filaTabla([$f->nombre_completo, $f->ci ?? 's/d', $f->correo, $f->total_prestamos], $anchos);
                    }
                } else {
                    $pdf->filaTabla(["Sin datos registrados aun.", "", "", ""], $anchos);
                }
                return $pdf->descargar("reporte_usuarios_mas_activos.pdf");
        }

        abort(404);
    }

    // Estudiantes registrados agrupados por día, mes o año
    private function registrosPor(string $periodo, ?int $limite = null)
    {
        $formato = ['dia' => '%Y-%m-%d', 'mes' => '%Y-%m', 'anio' => '%Y'][$periodo] ?? '%Y-%m-%d';

        $consulta = Usuario::where('rol', 'estudiante')
            ->select(DB::raw("DATE_FORMAT(fecha_registro, '$formato') AS fecha_grupo"), DB::raw('COUNT(*) AS total'))
            ->groupBy('fecha_grupo')
            ->orderByDesc('fecha_grupo');

        if ($limite) {
            $consulta->limit($limite);
        }

        return $consulta->get();
    }
}