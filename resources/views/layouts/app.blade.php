<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Biblioteca Ciencias del Desarrollo - UPEA')</title>
    <link rel="stylesheet" href="{{ asset('css/estilo.css') }}">
    <script src="{{ asset('js/responsive.js') }}" defer></script>
    @stack('estilos')
</head>
<body>
    @yield('navbar')

    @yield('contenido')

    <footer>Sistema Bibliotecario - Carrera de Ciencias del Desarrollo - UPEA &copy; {{ date('Y') }}</footer>

    @stack('scripts')
</body>
</html>