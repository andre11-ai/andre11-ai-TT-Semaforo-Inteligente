<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Tiempos Semafóricos - Sistema de Tráfico</title>
    <style>
        body { background-color: #f8fafc; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-dark { background-color: #1a202c; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header-title { font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .header-separator { color: #4a5568; margin: 0 5px; }
        .header-subtitle { color: #a0aec0; font-weight: 400; font-size: 0.95rem; }
        .btn-volver { background-color: transparent; color: white; border: 1px solid #4a5568; padding: 6px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; transition: 0.2s; }
        .btn-volver:hover { background-color: #2d3748; }

        .main-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }

        .card-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #edf2f7; }
        .card-title { font-size: 1.5rem; color: #1a202c; margin: 0; font-weight: bold; }
        .card-subtitle { color: #718096; font-size: 0.95rem; margin-top: 5px; }

        .filtros-container { display: flex; gap: 15px; background: #f7fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0; align-items: flex-end; flex-wrap: wrap;}
        .form-group { display: flex; flex-direction: column; flex: 1; min-width: 150px; }
        .form-group label { font-size: 0.85rem; font-weight: bold; color: #4a5568; margin-bottom: 5px; text-transform: uppercase;}
        .form-group input, .form-group select { padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 5px; font-family: inherit; font-size: 0.95rem;}
        .btn-filtrar { background: #3b5998; color: white; border: none; padding: 9px 20px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-filtrar:hover { background: #2a406d; }
        .btn-limpiar { background: #e2e8f0; color: #4a5568; padding: 9px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; text-align: center; font-size: 0.9rem;}

        .table-wrapper {
            max-height: 500px; 
            overflow-y: auto;
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.02);
        }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8fafc;
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
        }
        .data-table td { padding: 12px 15px; border-bottom: 1px solid #edf2f7; color: #2d3748; font-size: 0.95rem; vertical-align: middle; }
        .data-table tr:hover { background-color: #f7fafc; }

        .modo-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; display: inline-block; }
        .modo-ia { background: #ebf8ff; color: #2b6cb0; }
        .modo-fijo { background: #edf2f7; color: #4a5568; }
        .modo-forzado { background: #fff5f5; color: #c53030; }
        .modo-mantenimiento { background: #fffff0; color: #b7791f; }

        .tiempo-box { display: inline-block; width: 35px; text-align: center; padding: 4px 0; border-radius: 4px; font-weight: bold; color: white; font-size: 0.85rem;}
        .bg-verde { background-color: #48bb78; }
        .bg-amarillo { background-color: #ecc94b; color: #744210; }
        .bg-rojo { background-color: #f56565; }
    </style>
</head>
<body>

    <nav class="header-dark">
        <div class="header-title">
            <span>🚦 Sistema de Tráfico</span>
            <span class="header-separator">|</span>
            <span class="header-subtitle">Gestión de Prototipos</span>
        </div>
        <a href="{{ route('prototipos.index') }}" class="btn-volver">⬅ Volver </a>
    </nav>

    <main class="main-container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Historial de Tiempos Semafóricos</h1>
                <p class="card-subtitle">Registro auditable de cada ciclo calculado por la Inteligencia Artificial y los cambios manuales forzados.</p>
            </div>

            <form method="GET" action="{{ route('historial.ciclos') }}" class="filtros-container">
                <div class="form-group">
                    <label>Intersección</label>
                    <select name="interseccion_id">
                        <option value="">Todas las Intersecciones</option>
                        @foreach ($intersecciones as $int)
                            <option value="{{ $int->id }}" {{ request('interseccion_id') == $int->id ? 'selected' : '' }}>
                                {{ $int->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Modo de Operación</label>
                    <select name="modo">
                        <option value="">Todos los Modos</option>
                        <option value="Inteligente" {{ request('modo') == 'Inteligente' ? 'selected' : '' }}>Inteligente (IA)</option>
                        <option value="Fijo" {{ request('modo') == 'Fijo' ? 'selected' : '' }}>Fijo</option>
                        <option value="Mantenimiento" {{ request('modo') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="Rojo Intermitente" {{ request('modo') == 'Rojo Intermitente' ? 'selected' : '' }}>Rojo Intermitente</option>
                        <option value="Preventivo" {{ request('modo') == 'Preventivo' ? 'selected' : '' }}>Preventivo (Ámbar)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Día (Fecha)</label>
                    <input type="date" name="fecha" value="{{ request('fecha') }}">
                </div>
                <div class="form-group">
                    <label>Hora Exacta</label>
                    <input type="number" name="hora" min="0" max="23" placeholder="Ej. 14 (Para 2 PM)" value="{{ request('hora') }}">
                </div>
                <button type="submit" class="btn-filtrar">🔍 Aplicar Filtros</button>
                <a href="{{ route('historial.ciclos') }}" class="btn-limpiar">Limpiar</a>
            </form>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Intersección</th>
                            <th>Modo Detectado</th>
                            <th style="text-align: center;">T. Verde</th>
                            <th style="text-align: center;">T. Amarillo</th>
                            <th style="text-align: center;">T. Rojo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registros as $log)
                            <tr>
                                <td style="color: #718096; font-weight: 500;">
                                    <!-- AQUI ESTÁ LA CORRECCIÓN DE LA HORA -->
                                    📅 {{ \Carbon\Carbon::parse($log->created_at)->setTimezone('America/Mexico_City')->format('d/m/Y') }} <br>
                                    ⏱️ {{ \Carbon\Carbon::parse($log->created_at)->setTimezone('America/Mexico_City')->format('H:i:s') }}
                                </td>
                                <td style="font-weight: bold;">
                                    {{ $log->interseccion->nombre ?? 'Intersección Eliminada' }}
                                </td>
                                <td>
                                    @if (strtolower($log->modo_operativo) == 'inteligente')
                                        <span class="modo-badge modo-ia">🧠 Inteligente</span>
                                    @elseif (strtolower($log->modo_operativo) == 'fijo')
                                        <span class="modo-badge modo-fijo">⚙️ Fijo</span>
                                    @elseif (strtolower($log->modo_operativo) == 'mantenimiento')
                                        <span class="modo-badge modo-mantenimiento">🔧 Mantenimiento</span>
                                    @else
                                        <span class="modo-badge modo-forzado">⚠️ {{ $log->modo_operativo }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span class="tiempo-box bg-verde">{{ $log->tiempo_verde }}s</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="tiempo-box bg-amarillo">{{ $log->tiempo_amarillo }}s</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="tiempo-box bg-rojo">{{ $log->tiempo_rojo }}s</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #a0aec0; padding: 30px;">
                                    No se encontraron registros de ciclos semafóricos con esos filtros.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $registros->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </main>
</body>
</html>
