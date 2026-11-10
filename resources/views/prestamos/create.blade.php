@extends('layouts.app')

@section('titulo', 'Registrar Préstamo - Biblioteca Ciencias del Desarrollo - UPEA')

@section('navbar')
    @include('partials.navbar_' . $panel)
@endsection

@section('contenido')
    <div class="contenedor">
        <h1>Registrar Préstamo Presencial</h1>
        <p class="subtitulo">Este préstamo es presencial: la persona que retira el libro no necesita tener una cuenta en el sistema, solo se registran sus datos básicos.</p>

        <div class="tarjeta">
            @include('partials.errores')

            <form method="POST" action="{{ route($panel . '.prestamos.store') }}">
                @csrf
                <label>Buscar y seleccionar libro *</label>
                <input type="text" id="buscar-libro" placeholder="Escribe el título, autor o código para filtrar la lista..." oninput="filtrarListboxLibros()">
                <select name="id_libro" id="select-libro" size="8" required style="margin-top:8px;">
                    @foreach ($libros as $l)
                        <option value="{{ $l->id_libro }}" @selected(old('id_libro') == $l->id_libro)>
                            {{ $l->codigo . ' — ' . $l->titulo . ' (' . $l->autor . ') · Disp: ' . $l->cantidad_disponible }}
                        </option>
                    @endforeach
                </select>

                <h3 style="margin-top:22px;">Datos de quien retira el libro</h3>
                <div class="form-grid">
                    <div>
                        <label>Nombre completo *</label>
                        <input type="text" name="nombre_prestatario" required value="{{ old('nombre_prestatario') }}">
                    </div>
                    <div>
                        <label>Carnet de identidad (CI) *</label>
                        <input type="text" name="ci_prestatario" required value="{{ old('ci_prestatario') }}">
                    </div>
                </div>
                <div class="form-grid">
                    <div>
                        <label>Carrera</label>
                        <input type="text" name="carrera_prestatario" value="{{ old('carrera_prestatario', 'Ciencias del Desarrollo') }}">
                    </div>
                    <div>
                        <label>Teléfono / Celular</label>
                        <input type="text" name="telefono_prestatario" value="{{ old('telefono_prestatario') }}">
                    </div>
                </div>

                <div class="form-grid">
                    <div>
                        <label>Fecha de préstamo *</label>
                        <input type="date" name="fecha_prestamo" required value="{{ old('fecha_prestamo', date('Y-m-d')) }}">
                    </div>
                    <div>
                        <label>Fecha de devolución esperada *</label>
                        <input type="date" name="fecha_devolucion_esperada" required value="{{ old('fecha_devolucion_esperada', date('Y-m-d', strtotime('+7 days'))) }}">
                    </div>
                </div>

                <label>Observaciones</label>
                <textarea name="observaciones" rows="3" placeholder="Opcional">{{ old('observaciones') }}</textarea>

                <button type="submit" class="btn btn-exito">Registrar Préstamo</button>
                <a href="{{ route($panel . '.prestamos.index') }}" class="btn btn-secundario">Cancelar</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/buscar_libro.js') }}"></script>
@endpush