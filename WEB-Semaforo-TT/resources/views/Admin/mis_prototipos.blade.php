<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Prototipos - Sistema de Tráfico</title>
    <link rel="stylesheet" href="{{ asset('./CSS/Mis-prototipos.css') }}">
</head>
<body>

    <nav class="header-dark">
        <div class="header-title">
            <span>🚦 Sistema de Tráfico</span>
            <span class="header-separator">|</span>
            <span class="header-subtitle">Gestión de Prototipos</span>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-volver">⬅ Volver</a>
    </nav>

    <main class="main-container">

        @if(session('success'))
            <div style="background: #c6f6d5; color: #2f855a; padding: 15px; border-radius: 6px; border: 1px solid #9ae6b4; margin-bottom: 20px;">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div style="background: #fed7d7; color: #c53030; padding: 15px; border-radius: 6px; border: 1px solid #feb2b2; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #edf2f7;">
                <div>
                    <h1 class="card-title" style="font-size: 1.5rem; color: #1a202c; margin: 0; font-weight: bold;">Mis Prototipos</h1>
                    <p class="card-subtitle" style="color: #718096; font-size: 0.95rem; margin-top: 5px;">Monitorea y gestiona las intersecciones registradas en el sistema.</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('historial.ciclos') }}" style="background: #edf2f7; color: #4a5568; border: 1px solid #cbd5e0; padding: 10px 20px; border-radius: 6px; font-weight: bold; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.95rem; transition: 0.3s;">
                        📊 Historial de Tiempos
                    </a>
                    <button type="button" class="btn-primary" style="background: #3b5998; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; font-size: 0.95rem;" onclick="abrirModal('modalCrear')">
                        ➕ Nuevo Prototipo
                    </button>
                </div>
            </div>

            <table class="clean-table">
                <thead>
                    <tr>
                        <th># ID</th>
                        <th>Intersección</th>
                        <th>Dirección IP</th>
                        <th>Modo de Operación</th>
                        <th>Estado de Red</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($intersecciones as $interseccion)
                    <tr>
                        <td class="text-bold">{{ str_pad($interseccion->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="text-bold">{{ $interseccion->nombre }}</td>
                        <td style="color: #718096;">{{ $interseccion->ip_controlador }}</td>
                        <td>
                            <span class="badge {{ strtolower($interseccion->estado_operativo) }}">
                                ⚙️️ {{ $interseccion->estado_operativo }}
                            </span>
                        </td>
                        <td>
                            <span class="status-dot status-online"></span> <span style="font-size: 0.85rem; color:#4a5568;">En línea</span>
                        </td>
                        <td>
                            <div class="action-container">
                                <a href="{{ route('monitor.detalle', $interseccion->id) }}" class="action-btn" title="Monitor en Vivo">
                                    👁️
                                </a>
                                <button type="button" class="action-btn" title="Editar Hardware"
                                    onclick="openEditModal({{ $interseccion->id }}, '{{ $interseccion->nombre }}', '{{ $interseccion->ip_controlador }}', {{ $interseccion->tiempo_verde }}, {{ $interseccion->tiempo_amarillo }}, {{ $interseccion->tiempo_rojo }}, '{{ $interseccion->estado_operativo }}')">
                                    ✏️
                                </button>
                                <button type="button" class="action-btn delete" title="Eliminar Prototipo"
                                    onclick="openDeleteModal({{ $interseccion->id }}, '{{ $interseccion->nombre }}')">
                                    🗑️️
                                </button>
                                <button type="button" class="action-btn" style="color: #d97706;" title="Forzar Estado Manual"
                                    onclick="openForzarModal({{ $interseccion->id }}, '{{ $interseccion->nombre }}')">
                                    ⚠️
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 40px 0; color:#a0aec0;">
                            No hay intersecciones registradas. ¡Agrega tu primer prototipo!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Prototipo</h3>
                <button type="button" class="close-btn" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Nombre de Intersección</label>
                    <input type="text" name="nombre" id="edit_nombre" required>
                </div>
                <div class="form-group">
                    <label>Dirección IP</label>
                    <input type="text" name="ip_controlador" id="edit_ip" required>
                </div>
                <div class="times-row">
                    <div class="form-group" style="flex: 1;">
                        <label style="color: #38a169;">Verde (s)</label>
                        <input type="number" name="tiempo_verde" id="edit_verde" min="15" max="90" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label style="color: #d69e2e;">Amarillo (s)</label>
                        <input type="number" name="tiempo_amarillo" id="edit_amarillo" min="3" max="6" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label style="color: #e53e3e;">Rojo (s)</label>
                        <input type="number" name="tiempo_rojo" id="edit_rojo" min="15" max="90" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Estado Operativo</label>
                    <select name="estado_operativo" id="edit_estado" required>
                        <option value="Inteligente">Modo Inteligente (IA)</option>
                        <option value="Fijo">Modo Fijo (Tiempos Base)</option>
                        <option value="Mantenimiento">Modo Mantenimiento (Apagado)</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-content" style="border-top: 4px solid #e53e3e;">
            <div class="modal-header">
                <h3 style="color: #c53030;">⚠️ Confirmar Eliminación</h3>
                <button type="button" class="close-btn" onclick="closeModal('deleteModal')">&times;</button>
            </div>
            <p style="color: #4a5568; margin-bottom: 20px;">
                ¿Estás seguro de que deseas eliminar la intersección <strong id="delete_nombre"></strong>?
                Esta acción desconectará el hardware y no se puede deshacer.
            </p>
            <form id="deleteForm" method="POST" style="display: flex; gap: 10px; justify-content: flex-end;">
                @csrf
                @method('DELETE')
                <button type="button" class="action-btn" onclick="closeModal('deleteModal')" style="padding: 10px 15px;">Cancelar</button>
                <button type="submit" class="btn-submit" style="background: #e53e3e; width: auto; margin: 0; padding: 10px 15px;">Sí, Eliminar</button>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="forzarModal">
        <div class="modal-content" style="border-top: 4px solid #e53e3e;">
            <div class="modal-header">
                <h3 style="color: #c53030; margin: 0;">Forzar Estado - <span id="forzar_nombre"></span></h3>
                <button type="button" class="close-btn" onclick="closeModal('forzarModal')">&times;</button>
            </div>
            <form id="forzarForm" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #4a5568;">Seleccionar Acción</label>
                    <select name="modo_forzado" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px;">
                        <option value="Rojo Intermitente">🔴 Rojo Intermitente (Alto Total de Emergencia)</option>
                        <option value="Ambar Intermitente">🟡 Ámbar Intermitente (Precaución)</option>
                        <option value="Apagado">⚫ Apagar Sistema (Mantenimiento)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #4a5568;">Motivo del cambio (Obligatorio)</label>
                    <textarea name="motivo" required rows="3" placeholder="Ej. Accidente en la vía, falla eléctrica, mantenimiento preventivo..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box;"></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="action-btn" onclick="closeModal('forzarModal')" style="padding: 10px 15px;">Cancelar</button>
                    <button type="submit" style="background: #e53e3e; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('./JS/Mis-prototipos.js') }}"></script>
</body>
</html>
