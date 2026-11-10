<nav class="navbar">
    <div class="logo">Biblioteca <span>Ciencias del Desarrollo</span> - UPEA - Panel Ayudantes</div>
    <div class="navbar-links">
        <a href="{{ route('ayudantes.dashboard') }}">Inicio</a>
        <a href="{{ route('ayudantes.libros.index') }}">Ver Libros</a>
        <a href="{{ route('ayudantes.prestamos.index') }}">Préstamos</a>
        <a href="{{ route('ayudantes.usuarios.index') }}">Usuarios</a>
        <a href="{{ route('ayudantes.opiniones.create') }}">Opiniones</a>
        <a href="{{ route('catalogo') }}">Ver catálogo público</a>
    </div>
    <div class="usuario-info">
        <span>{{ auth()->user()->nombre_completo }} ({{ ucfirst(auth()->user()->rol) }})</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn-salir">Salir</button>
        </form>
    </div>
</nav>