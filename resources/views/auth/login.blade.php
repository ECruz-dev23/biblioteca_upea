<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Panel Biblioteca Ciencias del Desarrollo - UPEA</title>
    <link rel="stylesheet" href="{{ asset('css/estilo.css') }}">
    <script src="{{ asset('js/responsive.js') }}" defer></script>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-caja">
            <h2>Panel del Personal</h2>
            <p class="subtitulo">Administrador, pasantes y becari@s - Biblioteca Ciencias del Desarrollo - UPEA</p>

            @if (session('error'))
                <div class="alerta alerta-error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" required placeholder="admin" autofocus>

                <label for="contrasena">Contraseña</label>
                <div class="campo-clave">
                    <input type="password" id="contrasena" name="contrasena" required placeholder="••••••••">
                    <button type="button" class="btn-ojo" onclick="alternarClave('contrasena', this)" aria-label="Mostrar contraseña">Ver</button>
                </div>

                <button type="submit" class="btn btn-primario">Ingresar</button>
            </form>
            <p class="subtitulo" style="margin-top:20px;">
                <a href="{{ route('catalogo') }}">← Volver al catálogo público</a>
            </p>
        </div>
    </div>

    <script src="{{ asset('js/mostrar_clave.js') }}"></script>
</body>
</html>