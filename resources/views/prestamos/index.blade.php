@extends('layouts.app')

@section('titulo', 'Préstamos - Biblioteca Ciencias del Desarrollo - UPEA')

@section('navbar')
    @include('partials.navbar_' . $panel)
@endsection

@section('contenido')
    <div class="contenedor">
        <div class="top-acciones">
            <h1>Gestión de Préstamos</h1>
            <a href="{{ route($panel . '.prestamos.create') }}" class="btn btn-primario">+ Registrar Préstamo</a>
        </div>

        @if (session('msg'))
            <div class="alerta alerta-exito">{{ session('msg') }}</div>
        @endif

        <div class="tarjeta">
            <div class="buscador">
                <a href="{{ route($panel . '.prestamos.index', ['estado' => 'todos']) }}" class="btn {{ $filtro == 'todos' ? 'btn-primario' : 'btn-secundario' }} btn-pequeno">Todos</a>
                <a href="{{ route($panel . '.prestamos.index', ['estado' => 'prestado']) }}" class="btn {{ $filtro == 'prestado' ? 'btn-primario' : 'btn-secundario' }} btn-pequeno">Prestados</a>
                <a href="{{ route($panel . '.prestamos.index', ['estado' => 'atrasado']) }}" class="btn {{ $filtro == 'atrasado' ? 'btn-primario' : 'btn-secundario' }} btn-pequeno">Atrasados</a>
                <a href="{{ route($panel . '.prestamos.index', ['estado' => 'devuelto']) }}" class="btn {{ $filtro == 'devuelto' ? 'btn-primario' : 'btn-secundario' }} btn-pequeno">Devueltos</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Libro</th>
                        <th>{{ $panel === 'admin' ? 'Estudiante' : 'Prestatario' }}</th>
                        <th>CI</th>
                        @if ($panel === 'admin')
                            <th>Origen</th>
                        @endif
                        <th>F. Préstamo</th>
                        <th>F. Esperada</th>
                        <th>F. Devolución</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prestamos as $p)
                        <tr>
                            <td>{{ $p->libro->titulo }} <br><small style="color:#6c757d;">{{ $p->libro->codigo }}</small></td>
                            <td>{{ $p->prestatario_nombre }}</td>
                            <td>{{ $p->prestatario_ci ?? '—' }}</td>
                            @if ($panel === 'admin')
                                <td>{{ $p->origen === 'estudiante' ? 'Web' : 'Presencial' }}</td>
                            @endif
                            <td>{{ $p->fecha_prestamo }}</td>
                            <td>{{ $p->fecha_devolucion_esperada }}</td>
                            <td>{{ $p->fecha_devolucion_real ?? '—' }}</td>
                            <td><span class="badge badge-{{ $p->estado }}">{{ ucfirst($p->estado) }}</span></td>
                            <td class="acciones">
                                @if ($p->estado !== 'devuelto')
                                    <form method="POST" action="{{ route($panel . '.prestamos.devolver', $p) }}" onsubmit="return confirm('¿Confirmar la devolución de este libro?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-exito btn-pequeno">Devolver</button>
                                    </form>
                                @else
                                    <span style="color:#6c757d; font-size:12px;">Finalizado</span>
                                @endif
                           </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $panel === 'admin' ? 9 : 8 }}">No hay préstamos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection