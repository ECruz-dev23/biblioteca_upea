@extends('layouts.app')

@section('titulo', 'Opiniones y Sugerencias - Biblioteca Ciencias del Desarrollo - UPEA')

@section('navbar')
    <nav class="navbar">
        <div class="logo">Biblioteca <span>Ciencias del Desarrollo</span> - UPEA</div>
        <div class="navbar-links">
            <a href="{{ route('catalogo') }}">← Volver al catálogo</a>
        </div>
        <div class="usuario-info">
            @auth
                <span>{{ auth()->user()->nombre_completo }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-salir">Salir</button>
                </form>
            @endauth
        </div>
    </nav>
@endsection

@section('contenido')
    <div class="contenedor" style="max-width:650px;">
        <h1>Opiniones y Sugerencias</h1>
        <p class="subtitulo">Tu opinión nos ayuda a mejorar el sistema bibliotecario. Cuéntanos qué funcionó bien, qué no, o qué te gustaría que tuviera.</p>

        <div class="tarjeta">
            @if (session('enviado'))
                <div class="alerta alerta-exito">¡Gracias! Tu observación fue registrada correctamente.</div>
                <a href="{{ route('opiniones.create') }}" class="btn btn-secundario">Enviar otra opinión</a>
            @else
                @include('partials.errores')
                <form method="POST" action="{{ route('opiniones.store') }}">
                    @csrf
                    <label>Tu nombre (opcional)</label>
                    <input type="text" name="nombre" placeholder="Puedes dejarlo en blanco para ser anónimo" value="{{ old('nombre', auth()->check() ? auth()->user()->nombre_completo : '') }}">

                    <label>Tu observación o sugerencia *</label>
                    <textarea name="mensaje" rows="5" required placeholder="Escribe aquí tu opinión sobre el sistema...">{{ old('mensaje') }}</textarea>

                    <button type="submit" class="btn btn-exito">Enviar Opinión</button>
                </form>
            @endif
        </div>
    </div>
@endsection