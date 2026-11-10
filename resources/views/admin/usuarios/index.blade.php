@extends('layouts.app')

@section('titulo', 'Usuarios - Biblioteca Ciencias del Desarrollo - UPEA')

@section('navbar')
    @include('partials.navbar_admin')
@endsection

@section('contenido')
    <div class="contenedor">
        <div class="top-acciones">
            <h1>Gestión de Usuarios</h1>
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primario">+ Agregar Ayudante</a>
        </div>
        <p class="subtitulo">Aquí aparecen todos los que inician sesión en el sistema: estudiantes (autoregistrados desde el catálogo), pasantes, becari@s y el administrador.</p>

        @if (session('msg'))
            <div class="alerta alerta-exito">{{ session('msg') }}</div>
        @endif

        <div class="tarjeta">
            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="buscador">
                <input type="text" name="q" placeholder="Buscar por nombre, usuario, correo, CI o registro..." value="{{ $busqueda }}">
                <select name="rol" style="max-width:200px;">
                    <option value="todos" @selected($filtro_rol == 'todos')>Todos los roles</option>
                    <option value="estudiante" @selected($filtro_rol == 'estudiante')>Estudiantes</option>
                    <option value="pasante" @selected($filtro_rol == 'pasante')>Pasantes</option>
                    <option value="becario" @selected($filtro_rol == 'becario')>Becari@s</option>
                    <option value="administrador" @selected($filtro_rol == 'administrador')>Administradores</option>
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
                        <th>Último acceso</th>
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
                            <td>{{ $u->ultima_conexion ? $u->ultima_conexion->format('d/m/Y H:i') : 'Nunca' }}</td>
                            <td>
                                <span class="badge {{ $u->estado == 'activo' ? 'badge-activo' : 'badge-inactivo' }}">
                                    {{ ucfirst($u->estado) }}
                               </span>
                           </td>
                            <td class="acciones">
                                <a href="{{ route('admin.usuarios.edit', $u) }}" class="btn btn-primario btn-pequeno">Editar</a>
                                @if ($u->id_usuario != auth()->id())
                                    <form method="POST" action="{{ route('admin.usuarios.destroy', $u) }}" onsubmit="return confirm('¿Eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-peligro btn-pequeno">Eliminar</button>
                                    </form>
                                @endif
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