<nav class="navbar">
    <div class="logo">Biblioteca <span>Ciencias del Desarrollo</span> - UPEA - Panel Admin</div>
    <div class="navbar-links">
        <a href="{{ route('admin.dashboard') }}">Inicio</a>
        <a href="{{ route('admin.libros.index') }}">Libros</a>
        <a href="{{ route('admin.usuarios.index') }}">Usuarios</a>
        <a href="{{ route('admin.prestamos.index') }}">Préstamos</a>
        <a href="{{ route('admin.reportes.index') }}">Reportes</a>
        <a href="{{ route('admin.opiniones.index') }}">Opiniones</a>
        <a href="{{ route('catalogo') }}">Ver catálogo público</a>
    </div>
    <div class="usuario-info">
        <span>{{ auth()->user()->nombre_completo }} ({{ auth()->user()->rol }})</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn-salir">Salir</button>
        </form>
    </div>
</nav>