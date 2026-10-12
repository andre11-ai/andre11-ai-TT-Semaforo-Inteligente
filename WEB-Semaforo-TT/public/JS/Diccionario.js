function abrirModal(id) {
    const modal = document.getElementById(id);
    modal.style.display = 'flex';
    setTimeout(() => modal.style.opacity = '1', 10);
}

function cerrarModal(id) {
    const modal = document.getElementById(id);
    modal.style.opacity = '0';
    setTimeout(() => modal.style.display = 'none', 300);
}

function abrirModalEditar(id, palabra, categoria, peso) {
    document.getElementById('edit_palabra').value = palabra;
    document.getElementById('edit_categoria').value = categoria;
    document.getElementById('edit_peso').value = peso;
    document.getElementById('formEditar').action = '/diccionario/' + id;
    abrirModal('modalEditar');
}

function abrirModalEliminar(id, palabra) {
    document.getElementById('delete_palabra').innerText = '"' + palabra + '"';
    document.getElementById('formEliminar').action = '/diccionario/' + id;
    abrirModal('modalEliminar');
}