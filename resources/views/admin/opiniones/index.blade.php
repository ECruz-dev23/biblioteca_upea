@extends('layouts.app')

@section('titulo', 'Opiniones - Panel Admin')

@section('navbar')
    @include('partials.navbar_admin')
@endsection

@section('contenido')
    <div class="contenedor">
        <div class="top-acciones">
            <h1>Opiniones y Sugerencias</h1>
            <a href="{{ route('admin.opiniones.pdf') }}" class="btn btn-peligro">Descargar reporte completo (PDF)</a>
        </div>
        <p class="subtitulo">Aquí llegan las observaciones enviadas desde el catálogo público (estudiantes/visitantes), el panel de ayudantes (pasantes/becari@s) y este panel. No se guardan en la base de datos, solo en un archivo de reporte.</p>

        <div class="tarjeta">
            <h2>Dejar una opinión</h2>
            @if (session('enviado'))
                <div class="alerta alerta-exito">¡Gracias! Tu observación fue registrada correctamente.</div>
            @elseif (session('error'))
                <div class="alerta alerta-error">{{ session('error') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.opiniones.store') }}">
                @csrf
                <label>Tu observación o sugerencia *</label>
                <textarea name="mensaje" rows="4" required placeholder="Escribe una observación sobre el sistema...">{{ old('mensaje') }}</textarea>
                <button type="submit" class="btn btn-exito">Enviar Opinión</button>
            </form>
        </div>

        <div class="tarjeta">
            <h2>Todas las opiniones recibidas ({{ count($todas) }})</h2>
            <table>
                <thead><tr><th>Fecha</th><th>Origen</th><th>Nombre</th><th>Mensaje</th></tr></thead>
                <tbody>
                    @forelse ($todas as $o)
                        <tr>
                            <td>{{ $o['fecha'] }}</td>
                            <td>{{ $o['origen'] }}</td>
                            <td>{{ $o['nombre'] }}</td>
                            <td>{{ $o['mensaje'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Aún no se ha recibido ninguna opinión.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection