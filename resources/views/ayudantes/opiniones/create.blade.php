@extends('layouts.app')

@section('titulo', 'Opiniones - Panel Ayudantes')

@section('navbar')
    @include('partials.navbar_ayudantes')
@endsection

@section('contenido')
    <div class="contenedor" style="max-width:650px;">
        <h1>Opiniones y Sugerencias</h1>
        <p class="subtitulo">Como parte del equipo que administra el sistema, tu opinión es muy valiosa para seguir mejorándolo. Estas observaciones las revisa el administrador.</p>

        <div class="tarjeta">
            @if (session('enviado'))
                <div class="alerta alerta-exito">¡Gracias! Tu observación fue registrada correctamente.</div>
                <a href="{{ route('ayudantes.opiniones.create') }}" class="btn btn-secundario">Enviar otra opinión</a>
            @else
                @include('partials.errores')
                <form method="POST" action="{{ route('ayudantes.opiniones.store') }}">
                    @csrf
                    <label>Tu observación o sugerencia *</label>
                    <textarea name="mensaje" rows="5" required placeholder="¿Qué mejorarías del sistema? ¿Algo no funciona bien?">{{ old('mensaje') }}</textarea>
                    <button type="submit" class="btn btn-exito">Enviar Opinión</button>
                </form>
            @endif
        </div>
    </div>
@endsection