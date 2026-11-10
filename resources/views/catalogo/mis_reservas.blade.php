@extends('layouts.app')

@section('titulo', 'Mis Reservas - Biblioteca Ciencias del Desarrollo - UPEA')

@section('navbar')
    <nav class="navbar">
        <div class="logo">Biblioteca <span>Ciencias del Desarrollo</span> - UPEA</div>
        <div class="navbar-links">
            <a href="{{ route('catalogo') }}">← Volver al catálogo</a>
        </div>
        <div class="usuario-info">
            <span>{{ auth()->user()->nombre_completo }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-salir">Salir</button>
            </form>
        </div>
    </nav>
@endsection

@section('contenido')
    <div class="contenedor">
        <h1>Mis Reservas</h1>
        <p class="subtitulo">Historial de libros que has reservado o prestado.</p>

        <div class="tarjeta">
            <table>
                <thead>
                    <tr>
                        <th>Libro</th>
                        <th>Autor</th>
                        <th>F. Préstamo</th>
                        <th>F. Esperada</th>
                        <th>F. Devolución</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prestamos as $p)
                        <tr>
                            <td>{{ $p->libro->titulo }}<br><small style="color:#6c757d;">{{ $p->libro->codigo }}</small></td>
                            <td>{{ $p->libro->autor }}</td>
                            <td>{{ $p->fecha_prestamo }}</td>
                            <td>{{ $p->fecha_devolucion_esperada }}</td>
                            <td>{{ $p->fecha_devolucion_real ?? '—' }}</td>
                            <td><span class="badge badge-{{ $p->estado }}">{{ ucfirst($p->estado) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Aún no has reservado ningún libro. <a href="{{ route('catalogo') }}">Explora el catálogo</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection