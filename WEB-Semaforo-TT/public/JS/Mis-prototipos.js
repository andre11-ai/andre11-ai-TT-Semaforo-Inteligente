function openForzarModal(id, nombre) {
    document.getElementById('forzar_nombre').textContent = nombre;
    document.getElementById('forzarForm').action = '/prototipos/' + id + '/forzar';
    openModal('forzarModal');
}

function openModal(id) { 
    document.getElementById(id).classList.add('active'); 
}

function closeModal(id) { 
    document.getElementById(id).classList.remove('active'); 
}

function openEditModal(id, nombre, ip, verde, amarillo, rojo, estado) {
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_ip').value = ip;
    document.getElementById('edit_verde').value = verde;
    document.getElementById('edit_amarillo').value = amarillo;
    document.getElementById('edit_rojo').value = rojo;

    const selectEstado = document.getElementById('edit_estado');
    for(let i=0; i < selectEstado.options.length; i++) {
        if(selectEstado.options[i].value.toLowerCase() === estado.toLowerCase()) {
            selectEstado.selectedIndex = i; break;
        }
    }

    document.getElementById('editForm').action = '/prototipos/' + id;
    openModal('editModal');
}

function openDeleteModal(id, nombre) {
    document.getElementById('delete_nombre').textContent = '"' + nombre + '"';
    document.getElementById('deleteForm').action = '/prototipos/' + id;
    openModal('deleteModal');
}

document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('active');
    });
});