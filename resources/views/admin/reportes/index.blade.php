@extends('layouts.app')

@section('titulo', 'Reportes - Biblioteca Ciencias del Desarrollo - UPEA')

@push('estilos')
    <style>@media print { .navbar, .no-imprimir { display: none !important; } }</style>
@endpush

@section('navbar')
    @include('partials.navbar_admin')
@endsection

@section('contenido')
    <div class="contenedor">
        <div class="top-acciones">
            <h1>Reportes del Sistema</h1>
            <button onclick="window.print()" class="btn btn-primario no-imprimir">Imprimir</button>
        </div>

        <div class="tarjeta">
            <div class="top-acciones">
                <h2>Libros más buscados / vistos</h2>
                <a href="{{ route('admin.reportes.pdf', 'mas_buscados') }}" class="btn btn-peligro btn-pequeno no-imprimir">Descargar PDF</a>
            </div>
            <table>
                <thead><tr><th>Código</th><th>Título</th><th>Veces visto</th></tr></thead>
                <tbody>
                    @forelse ($mas_buscados as $f)
                        <tr>
                            <td>{{ $f->codigo }}</td>
                            <td>{{ $f->titulo }}</td>
                            <td>{{ $f->contador_vistas }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">Aún no hay vistas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="tarjeta">
            <div class="top-acciones">
                <h2>Libros más reservados / prestados</h2>
                <a href="{{ route('admin.reportes.pdf', 'mas_prestados') }}" class="btn btn-peligro btn-pequeno no-imprimir">Descargar PDF</a>
            </div>
            <table>
                <thead><tr><th>Código</th><th>Título</th><th>Veces prestado</th></tr></thead>
                <tbody>
                    @forelse ($mas_prestados as $f)
                        <tr>
                            <td>{{ $f->codigo }}</td>
                            <td>{{ $f->titulo }}</td>
                            <td>{{ $f->veces }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">Sin datos aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="tarjeta">
            <div class="top-acciones">
                <h2>Usuarios (estudiantes) más activos</h2>
                <a href="{{ route('admin.reportes.pdf', 'usuarios_activos') }}" class="btn btn-peligro btn-pequeno no-imprimir">Descargar PDF</a>
            </div>
            <table>
                <thead><tr><th>Nombre</th><th>Correo</th><th>Total préstamos</th></tr></thead>
                <tbody>
                    @forelse ($usuarios_activos as $f)
                        <tr>
                            <td>{{ $f->nombre_completo }}</td>
                            <td>{{ $f->correo }}</td>
                            <td>{{ $f->total_prestamos }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">Sin datos aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="tarjeta">
            <h2>⏰ Préstamos atrasados</h2>
            <table>
                <thead><tr><th>Libro</th><th>Estudiante</th><th>CI</th><th>Fecha esperada</th></tr></thead>
                <tbody>
                    @forelse ($atrasados as $f)
                        <tr>
                            <td>{{ $f->libro->titulo }}</td>
                            <td>{{ $f->prestatario_nombre }}</td>
                            <td>{{ $f->prestatario_ci ?? '—' }}</td>
                            <td>{{ $f->fecha_devolucion_esperada }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No hay préstamos atrasados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="tarjeta">
            <h2>Libros sin stock disponible</h2>
            <table>
                <thead><tr><th>Código</th><th>Título</th><th>Disponible</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse ($bajo_stock as $f)
                        <tr>
                            <td>{{ $f->codigo }}</td>
                            <td>{{ $f->titulo }}</td>
                            <td>{{ $f->cantidad_disponible }}</td>
                            <td>{{ $f->cantidad_total }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Todos los libros tienen ejemplares disponibles.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="tarjeta">
            <div class="top-acciones">
                <h2>Usuarios registrados por fecha (Día / Mes / Año)</h2>
                <div class="no-imprimir" style="display:flex; gap:8px; flex-wrap:wrap;">
                    <a href="{{ route('admin.reportes.pdf', ['tipo' => 'usuarios_registrados', 'periodo' => 'dia']) }}" class="btn btn-peligro btn-pequeno">PDF por Día</a>
                    <a href="{{ route('admin.reportes.pdf', ['tipo' => 'usuarios_registrados', 'periodo' => 'mes']) }}" class="btn btn-peligro btn-pequeno">PDF por Mes</a>
                    <a href="{{ route('admin.reportes.pdf', ['tipo' => 'usuarios_registrados', 'periodo' => 'anio']) }}" class="btn btn-peligro btn-pequeno">PDF por Año</a>
                </div>
            </div>

            <div class="grid-reportes">
                <div>
                    <h3>Por Día</h3>
                    <table>
                        <thead><tr><th>Fecha</th><th>Registros</th></tr></thead>
                        <tbody>
                            @forelse ($reg_por_dia as $f)
                                <tr>
                                    <td>{{ $f->fecha_grupo }}</td>
                                    <td>{{ $f->total }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2">Sin registros aún.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div>
                    <h3>Por Mes</h3>
                    <table>
                        <thead><tr><th>Mes</th><th>Registros</th></tr></thead>
                        <tbody>
                            @forelse ($reg_por_mes as $f)
                                <tr>
                                    <td>{{ $f->fecha_grupo }}</td>
                                    <td>{{ $f->total }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2">Sin registros aún.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div>
                    <h3>Por Año</h3>
                    <table>
                        <thead><tr><th>Año</th><th>Registros</th></tr></thead>
                        <tbody>
                            @forelse ($reg_por_anio as $f)
                                <tr>
                                    <td>{{ $f->fecha_grupo }}</td>
                                    <td>{{ $f->total }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2">Sin registros aún.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tarjeta">
            <h2>Libros por categoría</h2>
            <table>
                <thead><tr><th>Categoría</th><th>Cantidad de títulos</th></tr></thead>
                <tbody>
                    @forelse ($por_categoria as $f)
                        <tr>
                            <td>{{ $f->nombre }}</td>
                            <td>{{ $f->total }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2">Sin datos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection