@extends('layouts.app')

@section('titulo', 'Usuarios - Panel Ayudantes')

@section('navbar')
    @include('partials.navbar_ayudantes')
@endsection

@section('contenido')
    <div class="contenedor">
        <div class="top-acciones">
            <h1>Estudiantes y Docentes</h1>
            <a href="{{ route('ayudantes.usuarios.create') }}" class="btn btn-primario">+ Registrar Usuario</a>
        </div>
        <p class="subtitulo">Personas registradas que pueden retirar y reservar libros. Para eliminar cuentas o administrar al personal, consulta con el administrador.</p>

        @if (session('msg'))
            <div class="alerta alerta-exito">{{ session('msg') }}</div>
        @endif

        <div class="tarjeta">
            <form method="GET" action="{{ route('ayudantes.usuarios.index') }}" class="buscador">
                <input type="text" name="q" placeholder="Buscar por nombre, usuario, correo, CI o registro..." value="{{ $busqueda }}">
                <select name="rol" style="max-width:200px;">
                    <option value="todos" @selected($filtro_rol == 'todos')>Todos</option>
                    <option value="estudiante" @selected($filtro_rol == 'estudiante')>Estudiantes</option>
                    <option value="docente" @selected($filtro_rol == 'docente')>Docentes</option>
                </select>
                <button type="submit" class="btn btn-primario">Buscar</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Nombre completo</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>CI</th>
                        <th>Rol</th>
                        <th>Materia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $u)
                        <tr>
                            <td>{{ $u->nombre_completo }}</td>
                            <td>{{ $u->usuario }}</td>
                            <td>{{ $u->correo }}</td>
                            <td>{{ $u->ci ?? '—' }}</td>
                            <td>{{ ucfirst($u->rol) }}</td>
                            <td>{{ $u->materia ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $u->estado == 'activo' ? 'badge-activo' : 'badge-inactivo' }}">
                                    {{ ucfirst($u->estado) }}
                               </span>
                           </td>
                            <td class="acciones">
                                <a href="{{ route('ayudantes.usuarios.edit', $u) }}" class="btn btn-primario btn-pequeno">Editar</a>
                           </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No se encontraron usuarios.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection