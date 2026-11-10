document.addEventListener('DOMContentLoaded', function () {

    // Menú hamburguesa en cada .navbar
    document.querySelectorAll('.navbar').forEach(function (nav) {
        var logo = nav.querySelector('.logo');
        if (!logo) return;

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'nav-toggle';
        btn.setAttribute('aria-label', 'Abrir menú');
        btn.setAttribute('aria-expanded', 'false');
        btn.innerHTML = '&#9776;';
        logo.after(btn);

        btn.addEventListener('click', function () {
            var abierto = nav.classList.toggle('abierto');
            btn.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            btn.innerHTML = abierto ? '&#10005;' : '&#9776;';
        });
    });

    // Dropdown de categorías con toque
    document.querySelectorAll('.dropdown-toggle').forEach(function (t) {
        t.addEventListener('click', function (e) {
            e.stopPropagation();
            t.parentElement.classList.toggle('abierto');
        });
    });
    document.addEventListener('click', function () {
        document.querySelectorAll('.dropdown.abierto').forEach(function (d) {
            d.classList.remove('abierto');
        });
    });

    // Tablas: scroll horizontal y etiquetas para vista en tarjetas
    document.querySelectorAll('table').forEach(function (tabla) {
        if (tabla.parentElement.classList.contains('tabla-responsive')) return;

        var envoltura = document.createElement('div');
        envoltura.className = 'tabla-responsive';
        tabla.parentNode.insertBefore(envoltura, tabla);
        envoltura.appendChild(tabla);

        var encabezados = Array.from(tabla.querySelectorAll('thead th'))
            .map(function (th) { return th.textContent.trim(); });

        tabla.querySelectorAll('tbody tr').forEach(function (fila) {
            var celdas = Array.from(fila.children);
            if (celdas.some(function (c) { return c.hasAttribute('colspan'); })) return;
            celdas.forEach(function (td, i) {
                td.setAttribute('data-label', encabezados[i] || '');
            });
        });
    });
});
