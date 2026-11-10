// =========================================================
// Filtro en vivo para el listbox de selección de libro
// usado en los formularios de registrar préstamo presencial
// (panel admin y panel de ayudantes).
// =========================================================
function filtrarListboxLibros() {
    const texto = document.getElementById('buscar-libro').value.toLowerCase();
    const opciones = document.getElementById('select-libro').options;

    for (let i = 0; i < opciones.length; i++) {
        const contenido = opciones[i].text.toLowerCase();
        opciones[i].style.display = contenido.indexOf(texto) > -1 ? '' : 'none';
    }
}
