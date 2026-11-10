@php($editar = $libro->exists)

@include('partials.errores')

<form method="POST" action="{{ $editar ? route('admin.libros.update', $libro) : route('admin.libros.store') }}">
    @csrf
    @if ($editar)
        @method('PUT')
    @endif

    <div class="form-grid">
        <div>
            <label>Código *</label>
            <input type="text" name="codigo" required placeholder="Ej: LIB-001" value="{{ old('codigo', $libro->codigo) }}">
        </div>
        <div>
            <label>Título *</label>
            <input type="text" name="titulo" required value="{{ old('titulo', $libro->titulo) }}">
        </div>
    </div>

    <div class="form-grid">
        <div>
            <label>Autor *</label>
            <input type="text" name="autor" required value="{{ old('autor', $libro->autor) }}">
        </div>
        <div>
            <label>Editorial</label>
            <input type="text" name="editorial" value="{{ old('editorial', $libro->editorial) }}">
        </div>
    </div>

    <div class="form-grid">
        <div>
            <label>Año de publicación</label>
            <input type="number" name="anio_publicacion" @unless($editar) min="1900" max="2100" @endunless value="{{ old('anio_publicacion', $libro->anio_publicacion) }}">
        </div>
        <div>
            <label>Categoría</label>
            <select name="id_categoria">
                @foreach ($categorias as $cat)
                    <option value="{{ $cat->id_categoria }}" @selected(old('id_categoria', $libro->id_categoria) == $cat->id_categoria)>{{ $cat->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-grid">
        <div>
            <label>{{ $editar ? 'Cantidad total de ejemplares *' : 'Cantidad de ejemplares *' }}</label>
            <input type="number" name="cantidad_total" min="1" required value="{{ old('cantidad_total', $libro->cantidad_total ?? 1) }}">
        </div>
        <div>
            <label>Ubicación (estante)</label>
            <input type="text" name="ubicacion" placeholder="Ej: Estante A-3" value="{{ old('ubicacion', $libro->ubicacion) }}">
        </div>
    </div>

    <label>Descripción (se muestra en el catálogo público)</label>
    <textarea name="descripcion" rows="3" placeholder="Breve resumen del libro...">{{ old('descripcion', $libro->descripcion) }}</textarea>

    @if ($editar)
        <label>Estado</label>
        <select name="estado">
            <option value="activo" @selected(old('estado', $libro->estado) == 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $libro->estado) == 'inactivo')>Inactivo</option>
        </select>
    @endif

    <button type="submit" class="btn btn-exito">{{ $editar ? 'Guardar Cambios' : 'Guardar Libro' }}</button>
    <a href="{{ route('admin.libros.index') }}" class="btn btn-secundario">Cancelar</a>
</form>