@extends('layouts.app')

@section('titulo', 'Panel Ayudantes - Biblioteca Ciencias del Desarrollo - UPEA')

@section('navbar')
    @include('partials.navbar_ayudantes')
@endsection

@section('contenido')
    <div class="contenedor">
        <h1>Bienvenido(a), {{ auth()->user()->nombre_completo }}</h1>
        <p class="subtitulo">Panel de apoyo para pasantes y becari@s. Puedes registrar préstamos presenciales y consultar el catálogo. Para editar o eliminar libros, consulta con el administrador.</p>

        <div class="resumen-grid">
            <div class="resumen-item">
                <h3>{{ $total_libros }}</h3>
                <p>Títulos en catálogo</p>
            </div>
            <div class="resumen-item">
                <h3>{{ $total_ejemplares }}</h3>
                <p>Ejemplares disponibles</p>
            </div>
            <div class="resumen-item">
                <h3>{{ $total_prestamos_activos }}</h3>
                <p>Préstamos activos</p>
            </div>
            <div class="resumen-item">
                <h3>{{ $total_atrasados }}</h3>
                <p>Préstamos atrasados</p>
            </div>
        </div>

        <div class="tarjeta">
            <div class="top-acciones">
                <h2>Últimos préstamos registrados</h2>
                <a href="{{ route('ayudantes.prestamos.create') }}" class="btn btn-primario">+ Registrar Préstamo</a>
            </div>
            <table>
                <thead>
                    <tr><th>Libro</th><th>Prestatario</th><th>F. préstamo</th><th>F. esperada</th><th>Estado</th></tr>
                </thead>
                <tbody>
                    @forelse ($ultimos as $p)
                        <tr>
                            <td>{{ $p->libro->titulo }}</td>
                            <td>{{ $p->prestatario_nombre }}</td>
                            <td>{{ $p->fecha_prestamo }}</td>
                            <td>{{ $p->fecha_devolucion_esperada }}</td>
                            <td><span class="badge badge-{{ $p->estado }}">{{ ucfirst($p->estado) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Aún no hay préstamos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection