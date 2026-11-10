@extends('layouts.app')

@section('titulo', 'Libros - Panel Ayudantes')

@section('navbar')
    @include('partials.navbar_ayudantes')
@endsection

@section('contenido')
    <div class="contenedor">
        <h1>Catálogo de Libros</h1>
        <p class="subtitulo">Vista de solo lectura. Para agregar, editar o eliminar libros contacta al administrador.</p>

        <div class="tarjeta">
            <form method="GET" action="{{ route('ayudantes.libros.index') }}" class="buscador">
                <input type="text" name="q" placeholder="Buscar por título, autor o código..." value="{{ $busqueda }}">
                <button type="submit" class="btn btn-primario">Buscar</button>
                @if ($busqueda !== '')
                    <a href="{{ route('ayudantes.libros.index') }}" class="btn btn-secundario">Limpiar</a>
                @endif
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Código</th><th>Título</th><th>Autor</th><th>Categoría</th><th>Disponibles</th><th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($libros as $l)
                        <tr>
                            <td>{{ $l->codigo }}</td>
                            <td>{{ $l->titulo }}</td>
                            <td>{{ $l->autor }}</td>
                            <td>{{ $l->categoria->nombre ?? '—' }}</td>
                            <td>{{ $l->cantidad_disponible }} / {{ $l->cantidad_total }}</td>
                            <td>
                                <span class="badge {{ $l->estado == 'activo' ? 'badge-activo' : 'badge-inactivo' }}">
                                    {{ ucfirst($l->estado) }}
                               </span>
                           </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No se encontraron libros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection