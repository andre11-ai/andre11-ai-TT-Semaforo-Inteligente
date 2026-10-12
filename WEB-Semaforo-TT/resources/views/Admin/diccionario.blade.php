<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diccionario PLN - Sistema de Tráfico</title>
    
    <link rel="stylesheet" href="{{ asset('./CSS/Diccionario.css') }}">
</head>
<body>

    <nav class="header-dark">
        <div class="header-title">
            <span>🚦 Sistema de Tráfico</span>
            <span class="header-separator">|</span>
            <span class="header-subtitle">Motor PLN - Diccionario de Pesos</span>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-volver">⬅ Volver </a>
    </nav>

    <main class="main-container">
        
        @if (session('success'))
            <div style="background: #c6f6d5; border-left: 4px solid #38a169; color: #276749; padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: bold;">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div style="background: #fff5f5; border-left: 4px solid #e53e3e; color: #c53030; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <strong>Error al procesar la solicitud:</strong>
                <ul style="margin: 5px 0 0 15px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div>
                    <h1 class="card-title">Términos NLP</h1>
                    <p class="card-subtitle">Gestiona las palabras clave, categorías y severidad para el análisis de IA.</p>
                </div>
                <button type="button" class="btn-primary" onclick="abrirModal('modalCrear')">
                    ➕ Nuevo Término
                </button>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Palabra Clave</th>
                        <th>Categoría</th>
                        <th>Nivel de Severidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($palabras as $item)
                        <tr>
                            <td style="color: #a0aec0; font-weight: bold;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td style="font-weight: bold; font-size: 1.05rem;">{{ $item->palabra }}</td>
                            <td><span class="tag-categoria">{{ $item->categoria }}</span></td>
                            <td>
                                @if ($item->peso >= 8)
                                    <span class="peso-badge peso-alto">{{ $item->peso }} Crítico</span>
                                @elseif ($item->peso >= 4)
                                    <span class="peso-badge peso-medio">{{ $item->peso }} Moderado</span>
                                @else
                                    <span class="peso-badge peso-bajo">{{ $item->peso }} Leve</span>
                                @endif
                            </td>
                            <td class="action-btns">
                                <button type="button" class="btn-icon" style="color: #3182ce;" title="Editar Término" 
                                    onclick="abrirModalEditar({{ $item->id }}, '{{ $item->palabra }}', '{{ $item->categoria }}', {{ $item->peso }})">
                                    ✏️
                                </button>
                                <button type="button" class="btn-icon" style="color: #e53e3e;" title="Eliminar Término" 
                                    onclick="abrirModalEliminar({{ $item->id }}, '{{ $item->palabra }}')">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalCrear" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="color: #3b5998; margin: 0;">Agregar Nuevo Término</h3>
                <button type="button" onclick="cerrarModal('modalCrear')" style="cursor: pointer; font-size: 24px; color: #a0aec0; border: none; background: transparent; line-height: 1;">&times;</button>
            </div>
            <form action="{{ route('diccionario.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Palabra Clave (En singular y minúsculas)</label>
                    <input type="text" name="palabra" required placeholder="Ej. choque, bache, lluvia">
                </div>
                <div class="form-group">
                    <label>Categoría</label>
                    <select name="categoria" required>
                        <option value="Accidente">Accidente</option>
                        <option value="Clima">Clima</option>
                        <option value="Tráfico">Tráfico pesado</option>
                        <option value="Infraestructura">Infraestructura (Baches, Obras)</option>
                        <option value="Social">Social (Manifestaciones)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Peso de Severidad (1 al 10)</label>
                    <input type="number" name="peso" min="1" max="10" required placeholder="1 = Leve | 10 = Crítico">
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 25px;">
                    <button type="button" onclick="cerrarModal('modalCrear')" style="background: #edf2f7; color: #4a5568; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">Cancelar</button>
                    <button type="submit" class="btn-primary" style="margin: 0;">Guardar Término</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditar" class="modal-overlay">
        <div class="modal-content" style="border-top-color: #3182ce;">
            <div class="modal-header">
                <h3 style="color: #3182ce; margin: 0;">Editar Término</h3>
                <button type="button" onclick="cerrarModal('modalEditar')" style="cursor: pointer; font-size: 24px; color: #a0aec0; border: none; background: transparent; line-height: 1;">&times;</button>
            </div>
            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Palabra Clave</label>
                    <input type="text" id="edit_palabra" name="palabra" required>
                </div>
                <div class="form-group">
                    <label>Categoría</label>
                    <select id="edit_categoria" name="categoria" required>
                        <option value="Accidente">Accidente</option>
                        <option value="Clima">Clima</option>
                        <option value="Tráfico">Tráfico pesado</option>
                        <option value="Infraestructura">Infraestructura (Baches, Obras)</option>
                        <option value="Social">Social (Manifestaciones)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Peso de Severidad</label>
                    <input type="number" id="edit_peso" name="peso" min="1" max="10" required>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 25px;">
                    <button type="button" onclick="cerrarModal('modalEditar')" style="background: #edf2f7; color: #4a5568; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">Cancelar</button>
                    <button type="submit" style="background: #3182ce; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEliminar" class="modal-overlay">
        <div class="modal-content" style="border-top-color: #e53e3e;">
            <div class="modal-header">
                <h3 style="color: #e53e3e; margin: 0;">Eliminar Término</h3>
                <button type="button" onclick="cerrarModal('modalEliminar')" style="cursor: pointer; font-size: 24px; color: #a0aec0; border: none; background: transparent; line-height: 1;">&times;</button>
            </div>
            <div style="margin-bottom: 25px; color: #4a5568; font-size: 0.95rem; line-height: 1.5;">
                ¿Estás seguro de que deseas eliminar permanentemente la palabra <strong id="delete_palabra" style="color: #e53e3e; font-size: 1.1rem;"></strong> del motor de Inteligencia Artificial? Esta acción no se puede deshacer.
            </div>
            <form id="formEliminar" method="POST">
                @csrf
                @method('DELETE')
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="cerrarModal('modalEliminar')" style="background: #edf2f7; color: #4a5568; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">Cancelar</button>
                    <button type="submit" style="background: #e53e3e; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">Sí, Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('./JS/Diccionario.js') }}"></script>
</body>
</html>