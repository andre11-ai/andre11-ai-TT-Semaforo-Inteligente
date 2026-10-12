function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});

function openEditModal(id, name, email, rol) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    
    const selectRol = document.getElementById('edit_rol');
    for (let i = 0; i < selectRol.options.length; i++) {
        if (selectRol.options[i].value.toLowerCase() === (rol || '').toLowerCase()) {
            selectRol.selectedIndex = i;
            break;
        }
    }

    document.getElementById('editForm').action = '/usuarios/' + id;
    openModal('editModal');
}

function openDeleteModal(id, name) {
    document.getElementById('delete_user_name').innerText = name;
    document.getElementById('deleteForm').action = '/usuarios/' + id;
    openModal('deleteModal');
}