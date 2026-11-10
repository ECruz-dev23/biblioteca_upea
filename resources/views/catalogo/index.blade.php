@extends('layouts.app')

@section('titulo', 'Catálogo Bibliotecario  - Ciencias del Desarrollo UPEA')

@section('navbar')
    <nav class="navbar">
        <div class="logo">Biblioteca <span>Ciencias del Desarrollo</span> - UPEA</div>
        <div class="navbar-links">
            <a href="{{ route('catalogo') }}" class="{{ ($vista == 'todos' && $categoria_id == '' && $busqueda == '') ? 'nav-activo' : '' }}">Todos los libros</a>

            <div class="dropdown">
                <span class="dropdown-toggle">Categorías ▾</span>
                <div class="dropdown-menu">
                    @foreach ($categorias_lista as $c)
                        <a href="{{ route('catalogo', ['categoria' => $c->id_categoria]) }}">{{ $c->nombre }}</a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('catalogo', ['vista' => 'mas_buscados']) }}" class="{{ $vista == 'mas_buscados' ? 'nav-activo' : '' }}">Más buscados</a>

            @if (auth()->check() && auth()->user()->esEstudiante())
                <a href="{{ route('reservas.index') }}">Mis reservas</a>
            @endif
            <a href="{{ route('opiniones.create') }}">Opiniones</a>
        </div>

        <div class="usuario-info">
            @auth
                <span>{{ auth()->user()->nombre_completo }}</span>
                @if (auth()->user()->esStaff())
                    <a href="{{ auth()->user()->rutaPanel() }}" class="btn-salir btn-panel">{{ auth()->user()->esAdmin() ? 'Panel Admin' : 'Mi panel' }}</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-salir">Salir</button>
                </form>
            @else
                <button class="btn-salir btn-login" onclick="abrirModal('modal-login')">Iniciar sesión</button>
            @endauth
        </div>
    </nav>
@endsection

@section('contenido')
    <div class="contenedor">
        <div class="cabecera-publica">
            <h1>Sistema Bibliotecario Web</h1>
            <p class="subtitulo">Carrera de Ciencias del Desarrollo - UPEA · Busca, consulta y reserva material bibliográfico en línea.</p>

            <form method="GET" action="{{ route('catalogo') }}" class="buscador buscador-grande">
                <input type="text" name="q" placeholder="Buscar por título, autor, código o editorial..." value="{{ $busqueda }}" autofocus>
                <button type="submit" class="btn btn-primario">Buscar</button>
                @if ($busqueda !== '' || $categoria_id !== '' || $vista !== 'todos')
                    <a href="{{ route('catalogo') }}" class="btn btn-secundario">Limpiar</a>
                @endif
            </form>
        </div>

        <h2 style="margin-top:10px;">{{ $titulo_seccion }}</h2>

        <div class="catalogo-grid">
            @forelse ($libros as $l)
                <div class="libro-card" onclick="verDetalle({{ $l->id_libro }})">
                    <div class="libro-card-icono"></div>
                    <h3>{{ $l->titulo }}</h3>
                    <p class="libro-autor">{{ $l->autor }}</p>
                    <p class="libro-categoria">{{ $l->categoria->nombre ?? 'General' }}</p>
                    <span class="badge {{ $l->cantidad_disponible > 0 ? 'badge-disponible' : 'badge-prestado' }}">
                        {{ $l->cantidad_disponible > 0 ? 'Disponible' : 'Sin ejemplares' }}
                   </span>
                    <button class="btn btn-primario btn-pequeno" style="margin-top:12px;width:100%;" onclick="event.stopPropagation(); verDetalle({{ $l->id_libro }})">Ver más / Reservar</button>
                </div>
            @empty
                <p class="subtitulo">No se encontraron libros con esos criterios.</p>
            @endforelse
        </div>
    </div>

    <!-- ===================== MODAL: LOGIN / REGISTRO ===================== -->
    <div class="modal-overlay" id="modal-login">
        <div class="modal-caja">
            <span class="modal-cerrar" onclick="cerrarModal('modal-login')">&times;</span>

            <div class="tabs">
                <span class="tab-link tab-activo" id="tab-login" onclick="cambiarTab('login')">Iniciar sesión</span>
                <span class="tab-link" id="tab-registro" onclick="cambiarTab('registro')">Crear cuenta</span>
            </div>

            <!-- Formulario Login -->
            <form id="form-login" onsubmit="return enviarLogin(event)">
                <div id="mensaje-login"></div>
                <label>Usuario</label>
                <input type="text" name="usuario" required autocomplete="username">
                <label>Contraseña</label>
                <div class="campo-clave">
                    <input type="password" name="contrasena" id="clave-login" required>
                    <button type="button" class="btn-ojo" onclick="alternarClave('clave-login', this)" aria-label="Mostrar contraseña">Ver</button>
                </div>
                <button type="submit" class="btn btn-primario" style="width:100%;">Ingresar</button>
                <p class="subtitulo" style="margin-top:10px;">¿Eres estudiante y no tienes cuenta? <a href="#" onclick="cambiarTab('registro');return false;">Regístrate aquí</a></p>
            </form>

            <!-- Formulario Registro (estudiantes) -->
            <form id="form-registro" style="display:none;" onsubmit="return enviarRegistro(event)">
                <div id="mensaje-registro"></div>
                <label>Nombre completo *</label>
                <input type="text" name="nombre_completo" required>
                <div class="form-grid">
                    <div>
                        <label>Usuario * (para iniciar sesión)</label>
                        <input type="text" name="usuario" required autocomplete="username">
                    </div>
                    <div>
                        <label>CI *</label>
                        <input type="text" name="ci" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div>
                        <label>Correo electrónico *</label>
                        <input type="email" name="correo" required>
                    </div>
                    <div>
                        <label>Registro universitario</label>
                        <input type="text" name="registro_universitario">
                    </div>
                </div>

                <div class="form-grid">
                    <div>
                        <label>Carrera</label>
                        <input type="text" name="carrera" value="Ciencias del Desarrollo">
                    </div>
                    <div>
                        <label>Teléfono</label>
                        <input type="text" name="telefono">
                    </div>
                </div>

                <div class="form-grid">
                    <div>
                        <label>Semestre *</label>
                        <input type="text" name="semestre" required placeholder="Ej: 6to semestre">
                    </div>
                    <div>
                        <label>Curso *</label>
                        <input type="text" name="curso" required placeholder="Ej: Paralelo A">
                    </div>
                </div>
     
                <label>Contraseña * (mínimo 6 caracteres)</label>
                <div class="campo-clave">
                    <input type="password" name="contrasena" id="clave-registro" required minlength="6">
                    <button type="button" class="btn-ojo" onclick="alternarClave('clave-registro', this)" aria-label="Mostrar contraseña">Ver</button>
                </div>
                <button type="submit" class="btn btn-exito" style="width:100%;">Crear cuenta y entrar</button>
            </form>
        </div>
    </div>

    <!-- ===================== MODAL: DETALLE DE LIBRO ===================== -->
    <div class="modal-overlay" id="modal-detalle">
        <div class="modal-caja" id="contenido-detalle">
            <span class="modal-cerrar" onclick="cerrarModal('modal-detalle')">&times;</span>
            <p>Cargando...</p>
        </div>
    </div>

    <!-- ===================== MODAL: FORMULARIO DE RESERVA ===================== -->
    <div class="modal-overlay" id="modal-reserva">
        <div class="modal-caja">
            <span class="modal-cerrar" onclick="cerrarReserva()">&times;</span>

            <div id="reserva-formulario">
                <h2>Solicitar préstamo</h2>
                <p class="subtitulo" id="reserva-titulo-libro"></p>

                <div class="reserva-info">
                    <div><span>Fecha de préstamo</span><strong>Hoy (automática)</strong></div>
                    <div><span>Plazo de devolución</span><strong>7 días</strong></div>
                </div>

                <form id="form-reserva" onsubmit="return enviarReserva(event)">
                    <div id="mensaje-reserva"></div>
                    <input type="hidden" name="id_libro" id="reserva-id-libro">
                    <label>Observaciones</label>
                    <textarea name="observaciones" rows="2" placeholder="Opcional"></textarea>
                    <button type="submit" id="btn-confirmar-reserva" class="btn btn-exito" style="width:100%;">Confirmar préstamo</button>
                </form>
            </div>

            <div id="reserva-confirmacion" class="confirmacion oculto">
                <div class="confirmacion-icono"></div>
                <h2>Préstamo registrado</h2>
                <p class="subtitulo">Tu préstamo se generó con la fecha de hoy.</p>
                <div class="confirmacion-detalle">
                    <div><span>Libro</span><strong id="conf-libro"></strong></div>
                    <div><span>Fecha de préstamo</span><strong id="conf-prestamo"></strong></div>
                    <div><span>Devolver hasta</span><strong id="conf-devolucion"></strong></div>
                </div>
                <p class="confirmacion-nota">Pasa por la biblioteca a recoger tu libro.</p>
                <div class="confirmacion-botones">
                    <a href="{{ route('reservas.index') }}" class="btn btn-exito">Ver mis préstamos</a>
                    <button type="button" class="btn btn-secundario" onclick="window.location.reload()">Seguir explorando</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.CONFIG_APP = {
            sesion: {{ auth()->check() ? 'true' : 'false' }},
            rol: @json(auth()->user()->rol ?? ''),
            csrf: @json(csrf_token()),
            urlLogin: @json(route('auth.login.ajax')),
            urlRegistro: @json(route('auth.registro')),
            urlDetalle: @json(url('catalogo/detalle')),
            urlReservar: @json(route('reservas.store'))
        };
    </script>
    <script src="{{ asset('js/mostrar_clave.js') }}"></script>
    <script src="{{ asset('js/catalogo.js') }}"></script>
@endpush