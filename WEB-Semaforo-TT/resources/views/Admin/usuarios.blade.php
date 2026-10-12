<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Tráfico</title>
    
    <link rel="stylesheet" href="{{ asset('./CSS/Usuarios.css') }}">
</head>
<body>

    <nav class="header-dark">
        <div class="header-title">
            <span>🚦 Sistema de Tráfico</span>
            <span class="header-separator">|</span>
            <span class="header-subtitle">Gestión de Usuarios</span>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-volver">⬅ Volver</a>
    </nav>

    <main class="main-container">
        
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="header-flex">
                <div>
                    <h2 class="card-title">Cuentas Activas</h2>
                    <p class="card-description">Administra los operadores del sistema y revisa sus operaciones.</p>
                </div>
                <button class="btn-new" onclick="openModal('registerModal')">➕ Registrar Operador</button>
            </div>

            <table class="clean-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $user)
                    <tr>
                        <td class="text-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if(strtolower($user->rol) === 'administrador' || strtolower($user->role) === 'admin')
                                <span class="badge-rol rol-admin">🛡️ Admin</span>
                            @else
                                <span class="badge-rol rol-operador">👤 Operador</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-container">
                                <button type="button" class="action-btn" title="Editar" 
                                    onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->rol }}')">
                                    ✏️
                                </button>
                                
                                <a href="{{ route('historial.index', ['usuario_id' => $user->id]) }}" class="action-btn" title="Ver Historial del Operador">
                                    📋
                                </a>

                                <button type="button" class="action-btn delete" title="Eliminar Usuario" 
                                    onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 40px 0; color:#a0aec0;">
                            No se encontraron operadores registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <div class="modal-overlay" id="registerModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Registrar Nuevo Operador</h3>
                <button type="button" class="close-btn" onclick="closeModal('registerModal')">&times;</button>
            </div>
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Correo Institucional</label>
                    <input type="email" name="email" placeholder="@alumno.ipn.mx" required>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="Mín. 8 chars, 1 mayús, 1 núm" required>
                </div>
                <div class="form-group">
                    <label>Rol del Sistema</label>
                    <select name="rol" required>
                        <option value="Usuario">Operador Vial (Usuario)</option>
                        <option value="Administrador">Administrador</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Guardar Cuenta</button>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Operador</h3>
                <button type="button" class="close-btn" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Correo Institucional</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label>Nueva Contraseña (Opcional)</label>
                    <input type="password" name="password" placeholder="Dejar en blanco para conservar actual">
                </div>
                <div class="form-group">
                    <label>Rol del Sistema</label>
                    <select name="rol" id="edit_rol" required>
                        <option value="Usuario">Operador Vial (Usuario)</option>
                        <option value="Administrador">Administrador</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Actualizar Cuenta</button>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-content" style="border-top: 4px solid #e53e3e;">
            <div class="modal-header">
                <h3 style="color: #c53030;">⚠️ Confirmar Baja</h3>
                <button type="button" class="close-btn" onclick="closeModal('deleteModal')">&times;</button>
            </div>
            <p style="color: #4a5568; margin-bottom: 20px;">
                ¿Estás completamente seguro de que deseas eliminar al operador <strong id="delete_user_name"></strong>? 
                Esta acción no se puede deshacer y el usuario perderá acceso al sistema.
            </p>
            <form id="deleteForm" method="POST" style="display: flex; gap: 10px; justify-content: flex-end;">
                @csrf
                @method('DELETE')
                <button type="button" class="action-btn" onclick="closeModal('deleteModal')" style="padding: 10px 15px;">Cancelar</button>
                <button type="submit" class="btn-submit" style="background: #e53e3e; width: auto; margin: 0; padding: 10px 15px;">Sí, Eliminar</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('./JS/Usuarios.js') }}"></script>
</body>
</html>