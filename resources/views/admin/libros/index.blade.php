@extends('layouts.app')

@section('titulo', 'Libros - Biblioteca Carrera Ciencias del Desarrollo')

@section('navbar')
    @include('partials.navbar_admin')
@endsection

@section('contenido')
    <div class="contenedor">
        <div class="top-acciones">
            <h1>GESTION DE LIBROS</h1>
            <a href="{{ route('admin.libros.create') }}" class="btn btn-primario">+ Agregar Libro</a>
        </div>

        @if (session('msg'))
            <div class="alerta alerta-exito">{{ session('msg') }}</div>
        @endif

        <div class="tarjeta">
            <form method="GET" action="{{ route('admin.libros.index') }}" class="buscador">
                <input type="text" name="q" placeholder="Buscar por título, autor o código..." value="{{ $busqueda }}">
                <button type="submit" class="btn btn-primario">Buscar</button>
                @if ($busqueda !== '')
                    <a href="{{ route('admin.libros.index') }}" class="btn btn-secundario">Limpiar</a>
                @endif
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Categoría</th>
                        <th>Disponibles</th>
                        <th>Vistas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
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
                            <td>{{ $l->contador_vistas }}</td>
                            <td>
                                <span class="badge {{ $l->estado == 'activo' ? 'badge-activo' : 'badge-inactivo' }}">
                                    {{ ucfirst($l->estado) }}
                               </span>
                           </td>
                            <td class="acciones">
                                <a href="{{ route('admin.libros.edit', $l) }}" class="btn btn-primario btn-pequeno">Editar</a>
                                <form method="POST" action="{{ route('admin.libros.destroy', $l) }}" onsubmit="return confirm('¿Está seguro de eliminar/desactivar este libro?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-peligro btn-pequeno">Eliminar</button>
                                </form>
                           </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No se encontraron libros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection