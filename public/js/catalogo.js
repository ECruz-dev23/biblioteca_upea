// =========================================================
// Catálogo Público - Biblioteca UPEA (versión Laravel)
// Las URLs y el token CSRF vienen de window.CONFIG_APP,
// definido en resources/views/catalogo/index.blade.php
// =========================================================

const CFG = window.CONFIG_APP;
let sesionActiva = CFG.sesion;
let rolActivo = CFG.rol;
let idLibroSeleccionado = null;

function esc(texto) {
    return String(texto ?? '').replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
}

function enviarPost(url, datos) {
    return fetch(url, {
        method: 'POST',
        body: datos,
        headers: { 'X-CSRF-TOKEN': CFG.csrf, 'Accept': 'application/json' }
    }).then(r => r.json());
}

// ---------- Modales genéricos ----------
function abrirModal(id) {
    document.getElementById(id).classList.add('activo');
}
function cerrarModal(id) {
    document.getElementById(id).classList.remove('activo');
}

// Cerrar modal si se hace click fuera de la caja
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('activo');
    }
});

// Mostrar el modal de login automáticamente al cargar (no obligatorio)
window.addEventListener('load', function () {
    if (!sesionActiva) {
        setTimeout(function () { abrirModal('modal-login'); }, 500);
    }
});

// ---------- Tabs Login / Registro ----------
function cambiarTab(tab) {
    const formLogin = document.getElementById('form-login');
    const formRegistro = document.getElementById('form-registro');
    const tabLogin = document.getElementById('tab-login');
    const tabRegistro = document.getElementById('tab-registro');

    if (tab === 'login') {
        formLogin.style.display = 'block';
        formRegistro.style.display = 'none';
        tabLogin.classList.add('tab-activo');
        tabRegistro.classList.remove('tab-activo');
    } else {
        formLogin.style.display = 'none';
        formRegistro.style.display = 'block';
        tabLogin.classList.remove('tab-activo');
        tabRegistro.classList.add('tab-activo');
    }
}

// ---------- Login ----------
function enviarLogin(e) {
    e.preventDefault();
    const datos = new FormData(e.target);
    const msj = document.getElementById('mensaje-login');
    msj.innerHTML = '';

    enviarPost(CFG.urlLogin, datos)
        .then(data => {
            if (data.exito) {
                if (data.redirigir) {
                    window.location.href = data.redirigir;
                } else {
                    window.location.reload();
                }
            } else {
                msj.innerHTML = '<div class="alerta alerta-error">' + esc(data.mensaje) + '</div>';
            }
        })
        .catch(() => { msj.innerHTML = '<div class="alerta alerta-error">Error de conexión.</div>'; });

    return false;
}

// ---------- Registro ----------
function enviarRegistro(e) {
    e.preventDefault();
    const datos = new FormData(e.target);
    const msj = document.getElementById('mensaje-registro');
    msj.innerHTML = '';

    enviarPost(CFG.urlRegistro, datos)
        .then(data => {
            if (data.exito) {
                window.location.reload();
            } else {
                msj.innerHTML = '<div class="alerta alerta-error">' + esc(data.mensaje) + '</div>';
            }
        })
        .catch(() => { msj.innerHTML = '<div class="alerta alerta-error">Error de conexión.</div>'; });

    return false;
}

// ---------- Detalle de libro ----------
function verDetalle(idLibro) {
    const contenedor = document.getElementById('contenido-detalle');
    contenedor.innerHTML = '<span class="modal-cerrar" onclick="cerrarModal(\'modal-detalle\')">&times;</span><p>Cargando...</p>';
    abrirModal('modal-detalle');

    fetch(CFG.urlDetalle + '/' + idLibro, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (!data.exito) {
                contenedor.innerHTML = '<span class="modal-cerrar" onclick="cerrarModal(\'modal-detalle\')">&times;</span><p>' + esc(data.mensaje) + '</p>';
                return;
            }

            const l = data.libro;
            idLibroSeleccionado = l.id_libro;

            let botonReservar = '';
            if (l.cantidad_disponible <= 0) {
                botonReservar = '<button class="btn btn-secundario" disabled style="width:100%;">Sin ejemplares disponibles</button>';
            } else if (!sesionActiva) {
                botonReservar = '<button class="btn btn-primario" style="width:100%;" onclick="loginRequeridoParaReservar()">Solicitar préstamo (inicia sesión primero)</button>';
            } else if (rolActivo !== 'estudiante') {
                botonReservar = '<button class="btn btn-secundario" disabled style="width:100%;">Solo estudiantes pueden reservar</button>';
            } else {
                botonReservar = '<button class="btn btn-exito" style="width:100%;" onclick="abrirFormularioReserva(' + l.id_libro + ', ' + esc(JSON.stringify(l.titulo)) + ')">Solicitar préstamo</button>';
            }

            contenedor.innerHTML = `
                <span class="modal-cerrar" onclick="cerrarModal('modal-detalle')">&times;</span>
                <h2>${esc(l.titulo)}</h2>
                <p class="subtitulo">${esc(l.autor)}</p>
                <table style="margin-bottom:15px;">
                    <tr><td><strong>Código</strong></td><td>${esc(l.codigo)}</td></tr>
                    <tr><td><strong>Editorial</strong></td><td>${esc(l.editorial || '—')}</td></tr>
                    <tr><td><strong>Año</strong></td><td>${esc(l.anio_publicacion || '—')}</td></tr>
                    <tr><td><strong>Categoría</strong></td><td>${esc(l.categoria || 'General')}</td></tr>
                    <tr><td><strong>Ubicación</strong></td><td>${esc(l.ubicacion || '—')}</td></tr>
                    <tr><td><strong>Disponibilidad</strong></td><td>${l.cantidad_disponible} de ${l.cantidad_total} ejemplares</td></tr>
                </table>
                ${l.descripcion ? '<p>' + esc(l.descripcion) + '</p>' : ''}
                <div style="margin-top:15px;">${botonReservar}</div>
            `;
        })
        .catch(() => {
            contenedor.innerHTML = '<span class="modal-cerrar" onclick="cerrarModal(\'modal-detalle\')">&times;</span><p>Error al cargar el libro.</p>';
        });
}

function loginRequeridoParaReservar() {
    cerrarModal('modal-detalle');
    abrirModal('modal-login');
}

// ---------- Formulario de reserva (la fecha se genera automáticamente en el servidor) ----------
function abrirFormularioReserva(idLibro, titulo) {
    cerrarModal('modal-detalle');
    document.getElementById('reserva-id-libro').value = idLibro;
    document.getElementById('reserva-titulo-libro').innerText = titulo;
    document.getElementById('mensaje-reserva').innerHTML = '';
    document.getElementById('reserva-formulario').classList.remove('oculto');
    document.getElementById('reserva-confirmacion').classList.add('oculto');
    abrirModal('modal-reserva');
}

function cerrarReserva() {
    const confirmado = !document.getElementById('reserva-confirmacion').classList.contains('oculto');
    cerrarModal('modal-reserva');
    if (confirmado) { window.location.reload(); }
}

function enviarReserva(e) {
    e.preventDefault();
    const datos = new FormData(e.target);
    const msj = document.getElementById('mensaje-reserva');
    const boton = document.getElementById('btn-confirmar-reserva');
    msj.innerHTML = '';
    boton.disabled = true;
    boton.textContent = 'Registrando...';

    enviarPost(CFG.urlReservar, datos)
        .then(data => {
            boton.disabled = false;
            boton.textContent = 'Confirmar préstamo';
            if (data.exito) {
                document.getElementById('conf-libro').innerText = data.titulo;
                document.getElementById('conf-prestamo').innerText = data.fecha_prestamo;
                document.getElementById('conf-devolucion').innerText = data.fecha_devolucion;
                document.getElementById('reserva-formulario').classList.add('oculto');
                document.getElementById('reserva-confirmacion').classList.remove('oculto');
            } else {
                msj.innerHTML = '<div class="alerta alerta-error">' + esc(data.mensaje) + '</div>';
            }
        })
        .catch(() => {
            boton.disabled = false;
            boton.textContent = 'Confirmar préstamo';
            msj.innerHTML = '<div class="alerta alerta-error">Error de conexión.</div>';
        });

    return false;
}
