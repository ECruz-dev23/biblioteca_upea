@extends('layouts.app')

@section('titulo', 'Panel Admin - Biblioteca Ciencias del Desarrollo - UPEA')

@section('navbar')
    @include('partials.navbar_admin')
@endsection

@section('contenido')
    <div class="contenedor">
        <h1>Panel Principal</h1>
        <p class="subtitulo">Bienvenido(a), resumen general del sistema bibliotecario.</p>

        <div class="resumen-grid">
            <div class="resumen-item">
                <h3>{{ $total_libros }}</h3>
                <p>Títulos registrados</p>
            </div>
            <div class="resumen-item">
                <h3>{{ $total_ejemplares }}</h3>
                <p>Ejemplares disponibles</p>
            </div>
            <div class="resumen-item">
                <h3>{{ $total_estudiantes }}</h3>
                <p>Estudiantes registrados</p>
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
            <h2>Últimos préstamos / reservas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Libro</th>
                        <th>Estudiante</th>
                        <th>Origen</th>
                        <th>F. préstamo</th>
                        <th>F. esperada</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ultimos as $p)
                        <tr>
                            <td>{{ $p->libro->titulo }}</td>
                            <td>{{ $p->prestatario_nombre }}</td>
                            <td>{{ $p->origen === 'estudiante' ? 'Reserva web' : 'Registro presencial' }}</td>
                            <td>{{ $p->fecha_prestamo }}</td>
                            <td>{{ $p->fecha_devolucion_esperada }}</td>
                            <td><span class="badge badge-{{ $p->estado }}">{{ ucfirst($p->estado) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Aún no hay préstamos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection