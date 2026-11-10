@extends('layouts.app')

@section('titulo', 'Editar Usuario - Panel Ayudantes')

@push('estilos')
    <style>.oculto{display:none !important;}</style>
@endpush

@section('navbar')
    @include('partials.navbar_ayudantes')
@endsection

@section('contenido')
    <div class="contenedor">
        <h1>Editar Estudiante / Docente</h1>

        <div class="tarjeta">
            @include('partials.errores')

            <form method="POST" action="{{ route('ayudantes.usuarios.update', $usuario) }}">
                @csrf
                @method('PUT')

                <h3>Datos de acceso</h3>
                <div class="form-grid">
                    <div>
                        <label>Nombre completo *</label>
                        <input type="text" name="nombre_completo" required value="{{ old('nombre_completo', $usuario->nombre_completo) }}">
                    </div>
                    <div>
                        <label>Usuario * (para iniciar sesión)</label>
                        <input type="text" name="usuario" required autocomplete="username" value="{{ old('usuario', $usuario->usuario) }}">
                    </div>
                </div>
                <div class="form-grid">
                    <div>
                        <label>Correo electrónico *</label>
                        <input type="email" name="correo" required value="{{ old('correo', $usuario->correo) }}">
                    </div>
                    <div>
                        <label>Rol</label>
                        <select name="rol" id="campo-rol" onchange="mostrarSeccion()">
                            <option value="estudiante" @selected(old('rol', $usuario->rol) == 'estudiante')>Estudiante</option>
                            <option value="docente" @selected(old('rol', $usuario->rol) == 'docente')>Docente</option>
                        </select>
                    </div>
                </div>
                <div class="form-grid">
                    <div>
                        <label>Estado</label>
                        <select name="estado">
                            <option value="activo" @selected(old('estado', $usuario->estado) == 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado', $usuario->estado) == 'inactivo')>Inactivo</option>
                        </select>
                    </div>
                    <div>
                        <label>Nueva contraseña (dejar en blanco para no cambiarla)</label>
                        <div class="campo-clave">
                            <input type="password" name="nueva_contrasena" id="clave-editar" minlength="6">
                            <button type="button" class="btn-ojo" onclick="alternarClave('clave-editar', this)" aria-label="Mostrar contraseña">Ver</button>
                        </div>
                    </div>
                </div>

                <h3 style="margin-top:22px;">Datos personales</h3>
                <div class="form-grid">
                    <div>
                        <label>Carnet de identidad (CI) *</label>
                        <input type="text" name="ci" required value="{{ old('ci', $usuario->ci) }}">
                    </div>
                    <div>
                        <label>Registro universitario</label>
                        <input type="text" name="registro_universitario" value="{{ old('registro_universitario', $usuario->registro_universitario) }}">
                    </div>
                </div>
                <div class="form-grid">
                    <div>
                        <label>Carrera</label>
                        <input type="text" name="carrera" value="{{ old('carrera', $usuario->carrera) }}">
                    </div>
                    <div>
                        <label>Teléfono / Celular</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}">
                    </div>
                </div>
                <label>Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $usuario->direccion) }}">

                <!-- Solo DOCENTE -->
                <div id="bloque-docente" class="oculto">
                    <label>Materia que imparte *</label>
                    <input type="text" name="materia" id="campo-materia" value="{{ old('materia', $usuario->materia) }}">
                </div>
                <div id="bloque-curso">
                    <div class="form-grid">
                        <div>
                            <label>Semestre *</label>
                            <input type="text" name="semestre" id="campo-semestre" value="{{ old('semestre', $usuario->semestre) }}">
                        </div>
                        <div>
                            <label>Curso *</label>
                            <input type="text" name="curso" id="campo-curso" value="{{ old('curso', $usuario->curso) }}">
                        </div>
                    </div>
                </div>

                <h3 style="margin-top:22px;">Datos de referencia <small style="color:#6c757d;font-weight:normal;">(opcional)</small></h3>
                <div class="form-grid">
                    <div>
                        <label>Nombre completo</label>
                        <input type="text" name="nombre_referencia" value="{{ old('nombre_referencia', $usuario->nombre_referencia) }}">
                    </div>
                    <div>
                        <label>Teléfono</label>
                        <input type="text" name="telefono_referencia" value="{{ old('telefono_referencia', $usuario->telefono_referencia) }}">
                    </div>
                </div>
                <label>Dirección</label>
                <input type="text" name="direccion_referencia" value="{{ old('direccion_referencia', $usuario->direccion_referencia) }}">

                <button type="submit" class="btn btn-exito">Guardar Cambios</button>
                <a href="{{ route('ayudantes.usuarios.index') }}" class="btn btn-secundario">Cancelar</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/mostrar_clave.js') }}"></script>
    <script>
        function mostrarSeccion() {
            const rol = document.getElementById('campo-rol').value;
            const esDocente = (rol === 'docente');
            const esEstudiante = (rol === 'estudiante');
            document.getElementById('bloque-docente').classList.toggle('oculto', !esDocente);
            document.getElementById('bloque-curso').classList.toggle('oculto', !esEstudiante);
            document.getElementById('campo-materia').required = esDocente;
            document.getElementById('campo-semestre').required = esEstudiante;
            document.getElementById('campo-curso').required = esEstudiante;
        }
        mostrarSeccion();
    </script>
@endpush