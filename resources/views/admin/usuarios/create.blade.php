@extends('layouts.app')

@section('titulo', 'Agregar Usuario - Biblioteca Ciencias del Desarrollo - UPEA')

@push('estilos')
    <style>.oculto{display:none !important;}</style>
@endpush

@section('navbar')
    @include('partials.navbar_admin')
@endsection

@section('contenido')
    <div class="contenedor">
        <h1>Agregar Usuario</h1>
        <p class="subtitulo">Aquí se crean cuentas de pasantes, becari@s, estudiantes y docentes. Los estudiantes también pueden registrarse solos desde el catálogo público.</p>

        <div class="tarjeta">
            @include('partials.errores')

            <form method="POST" action="{{ route('admin.usuarios.store') }}">
                @csrf

                <h3>Datos de acceso</h3>
                <div class="form-grid">
                    <div>
                        <label>Nombre completo *</label>
                        <input type="text" name="nombre_completo" required value="{{ old('nombre_completo') }}">
                    </div>
                    <div>
                        <label>Usuario * (para iniciar sesión)</label>
                        <input type="text" name="usuario" required autocomplete="username" value="{{ old('usuario') }}">
                    </div>
                </div>
                <div class="form-grid">
                    <div>
                        <label>Correo electrónico *</label>
                        <input type="email" name="correo" required value="{{ old('correo') }}">
                    </div>
                    <div>
                        <label>Rol *</label>
                        <select name="rol" id="campo-rol" required onchange="mostrarSeccion()">
                            <option value="estudiante" @selected(old('rol') == 'estudiante')>Estudiante</option>
                            <option value="docente" @selected(old('rol') == 'docente')>Docente</option>
                            <option value="pasante" @selected(old('rol') == 'pasante')>Pasante</option>
                            <option value="becario" @selected(old('rol') == 'becario')>Becari@</option>
                        </select>
                    </div>
                </div>
                <div class="form-grid">
                    <div>
                        <label>Contraseña *</label>
                        <div class="campo-clave">
                            <input type="password" name="contrasena" id="clave-usuario" required minlength="6">
                            <button type="button" class="btn-ojo" onclick="alternarClave('clave-usuario', this)" aria-label="Mostrar contraseña">Ver</button>
                        </div>
                    </div>
                    <div></div>
                </div>

                <h3 style="margin-top:22px;">Datos personales</h3>
                <div class="form-grid">
                    <div>
                        <label>Carnet de identidad (CI) *</label>
                        <input type="text" name="ci" required value="{{ old('ci') }}">
                    </div>
                    <div>
                        <label>Registro universitario / Nro. de matrícula</label>
                        <input type="text" name="registro_universitario" value="{{ old('registro_universitario') }}">
                    </div>
                </div>
                <div class="form-grid">
                    <div>
                        <label>Carrera</label>
                        <input type="text" name="carrera" value="{{ old('carrera', 'Ciencias del Desarrollo') }}">
                    </div>
                    <div>
                        <label>Teléfono / Celular</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}">
                    </div>
                </div>
                        <label>Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion') }}">

                <!-- Solo DOCENTE -->
                <div id="bloque-docente" class="oculto">
                    <label>Materia que imparte *</label>
                    <input type="text" name="materia" id="campo-materia" placeholder="Ej: Metodología de la Investigación" value="{{ old('materia') }}">
                </div>

                <!-- ESTUDIANTE, PASANTE y BECARI@: semestre y curso -->
                <div id="bloque-curso" class="oculto">
                    <div class="form-grid">
                        <div>
                            <label>Semestre <span class="req-curso">*</span></label>
                            <input type="text" name="semestre" id="campo-semestre" placeholder="Ej: 6to semestre" value="{{ old('semestre') }}">
                        </div>
                        <div>
                            <label>Curso <span class="req-curso">*</span></label>
                            <input type="text" name="curso" id="campo-curso" placeholder="Ej: Paralelo A" value="{{ old('curso') }}">
                        </div>
                    </div>
                </div>

                <!-- Solo PASANTE / BECARI@ -->
                <div id="bloque-ayudante" class="oculto">
                    <label>Institución</label>
                    <input type="text" name="institucion" placeholder="Ej: UPEA" value="{{ old('institucion') }}">
                </div>

                <h3 style="margin-top:22px;">Datos de referencia <small style="color:#6c757d;font-weight:normal;">(opcional)</small></h3>
                <div class="form-grid">
                    <div>
                        <label>Nombre completo</label>
                        <input type="text" name="nombre_referencia" placeholder="Persona de contacto en caso de emergencia" value="{{ old('nombre_referencia') }}">
                    </div>
                    <div>
                        <label>Teléfono</label>
                        <input type="text" name="telefono_referencia" value="{{ old('telefono_referencia') }}">
                    </div>
                </div>
                <label>Dirección</label>
                <input type="text" name="direccion_referencia" value="{{ old('direccion_referencia') }}">

                <button type="submit" class="btn btn-exito">Guardar Usuario</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secundario">Cancelar</a>
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
            const esAyudante = (rol === 'pasante' || rol === 'becario');
            document.getElementById('bloque-docente').classList.toggle('oculto', !esDocente);
            document.getElementById('bloque-ayudante').classList.toggle('oculto', !esAyudante);
            document.getElementById('bloque-curso').classList.toggle('oculto', !(esEstudiante || esAyudante));
            document.querySelectorAll('.req-curso').forEach(e => e.classList.toggle('oculto', !esEstudiante));
            document.getElementById('campo-materia').required = esDocente;
            document.getElementById('campo-semestre').required = esEstudiante;
            document.getElementById('campo-curso').required = esEstudiante;
        }
        mostrarSeccion();
    </script>
@endpush