// =========================================================
// Botón "ojito" para mostrar/ocultar la contraseña
// Se usa en todos los formularios con campo de contraseña
// (login del personal, login/registro del catálogo,
// formularios de usuarios del panel admin).
// =========================================================
function alternarClave(idInput, boton) {
    const input = document.getElementById(idInput);
    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
        boton.textContent = 'Ocultar';
        boton.setAttribute('aria-label', 'Ocultar contraseña');
    } else {
        input.type = 'password';
        boton.textContent = 'Ver';
        boton.setAttribute('aria-label', 'Mostrar contraseña');
    }
}
