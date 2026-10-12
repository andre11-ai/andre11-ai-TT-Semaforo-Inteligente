<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Operaciones - Auditoría</title>
    <link rel="stylesheet" href="{{ asset('./CSS/Historial.css') }}">
</head>
<body>

    <nav class="header-dark">
        <div class="header-title">
            <span>🚦 Sistema de Tráfico</span>
            <span class="header-separator">|</span>
            <span class="header-subtitle">Auditoría del Sistema</span>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-volver">⬅ Volver </a>
    </nav>

    <main class="main-container">
        <div class="card">
            <h2 class="card-title">Historial de Operaciones</h2>
            <p class="card-description">Registro de todos los eventos y movimientos realizados por los operadores.</p>

            <form action="{{ route('historial.index') }}" method="GET" class="filters-bar">

                <div class="filter-group">
                    <label>Filtrar por Operador</label>
                    <select name="usuario_id" class="filter-input">
                        <option value="">-- Todos los Operadores --</option>
                        @foreach($usuarios as $user)
                            <option value="{{ $user->id }}" {{ request('usuario_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>Filtrar por Día</label>
                    <input type="date" name="fecha" class="filter-input" value="{{ request('fecha') }}">
                </div>

                <button type="submit" class="btn-filter">🔍 Aplicar Filtros</button>
                <a href="{{ route('historial.index') }}" class="btn-clear">Deshacer</a>
            </form>

            <div class="table-scroll-container">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Fecha y Hora</th>
                            <th style="width: 25%;">Operador</th>
                            <th style="width: 20%;">Acción Realizada</th>
                            <th style="width: 30%;">Detalles</th>
                            <th style="width: 10%;">Dirección IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $registro)
                        <tr>
                            <td class="date-text">{{ \Carbon\Carbon::parse($registro->created_at)->setTimezone('America/Mexico_City')->format('d/m/Y H:i:s') }}</td>

                            <td class="user-name">
                                {{ $registro->user->name }}
                                @if(strtolower($registro->user->rol ?? $registro->user->role) === 'administrador' || strtolower($registro->user->rol ?? $registro->user->role) === 'admin')
                                    <span class="badge-rol rol-admin">Admin</span>
                                @else
                                    <span class="badge-rol rol-operador">Operador</span>
                                @endif
                            </td>

                            <td>
                                @if(str_contains(strtolower($registro->accion), 'forzado') || str_contains(strtolower($registro->accion), 'eliminar') || str_contains(strtolower($registro->accion), 'eliminación'))
                                    <span class="badge-action badge-alerta">🚨 {{ $registro->accion }}</span>
                                @elseif(str_contains(strtolower($registro->accion), 'crear') || str_contains(strtolower($registro->accion), 'nuevo') || str_contains(strtolower($registro->accion), 'nueva'))
                                    <span class="badge-action badge-exito">✅ {{ $registro->accion }}</span>
                                @else
                                    <span class="badge-action badge-info">ℹ️ {{ $registro->accion }}</span>
                                @endif
                            </td>

                            <td style="font-size: 0.85rem; line-height: 1.4;">{{ $registro->detalles }}</td>
                            <td class="date-text" style="font-family: monospace;">{{ $registro->ip_address }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 60px 0; color:#a0aec0;">
                                <div style="font-size: 3rem; margin-bottom: 10px;">📭</div>
                                No se encontraron registros de operaciones con los filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>

</body>
</html>
