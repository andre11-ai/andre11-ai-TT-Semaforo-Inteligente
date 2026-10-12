<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor en Vivo - Sistema de Tráfico</title>
    <link rel="stylesheet" href="{{ asset('./CSS/Monitor.css') }}">
</head>
<body>

    <nav class="header-dark">
        <div class="header-title">
            <span>🚦 Sistema de Tráfico</span>
            <span class="header-separator">|</span>
            <span class="header-subtitle">Monitor de Tráfico en Vivo</span>
        </div>
        <a href="{{ route('monitor.global') }}" class="btn-volver">⬅ Volver</a>
    </nav>

    <main class="main-container">

        <!-- ============================================== -->
        <!-- SISTEMA DE NOTIFICACIONES (ÉXITO / ERROR)      -->
        <!-- ============================================== -->
        @if(session('success'))
            <div style="background: #c6f6d5; color: #276749; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #48bb78; font-weight: bold;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fed7d7; color: #9b2c2c; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #e53e3e;">
                <strong style="display: block; margin-bottom: 5px;">⚠️ Hubo un problema al aplicar el cambio:</strong>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- ============================================== -->

        <div class="alert-box" id="failsafe_alert" style="display: {{ (!$datosApi && strtolower($interseccion->estado_operativo) === 'inteligente') ? 'block' : 'none' }};">
            <h2 style="margin: 0 0 10px 0;">⚠️ Conexión Perdida con el Motor de Inteligencia Artificial</h2>
            <p style="margin: 0; font-size: 0.95rem;">El servidor Python no responde. El semáforo operará en <strong>Modo Failsafe (Offline)</strong> utilizando los tiempos base de seguridad hasta que se restablezca la red.</p>
        </div>

        <div class="dashboard-grid">

            <div class="card">
                <div class="card-header">
                    <span>📡 Hardware de Borde</span>
                    @if(strtolower($interseccion->estado_operativo) === 'inteligente')
                        <div class="sync-active">
                            <span style="display:inline-block; width:8px; height:8px; background:#38a169; border-radius:50%;"></span> Sincronizando (1h)
                        </div>
                    @else
                        <div class="sync-inactive">
                            <span style="display:inline-block; width:8px; height:8px; background:#a0aec0; border-radius:50%;"></span> Auto-Sync Apagado
                        </div>
                    @endif
                </div>

                <div class="detail-row">
                    <span class="detail-label">Intersección</span>
                    <span class="detail-value">{{ $interseccion->nombre }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">IP Controlador</span>
                    <span class="detail-value">{{ $interseccion->ip_controlador }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Modo Operativo</span>
                    <span class="detail-value">{{ $interseccion->estado_operativo }}</span>
                </div>
                <div class="detail-row" style="border: none;">
                    <span class="detail-label">Conexión a IA</span>
                    <span id="ai_status_badge" class="status-badge {{ $datosApi ? 'status-on' : 'status-off' }}">
                        {{ $datosApi ? 'EN LÍNEA' : 'OFFLINE' }}
                    </span>
                </div>

                <div style="margin-top: 25px; display: flex; flex-direction: column; gap: 10px; text-align: center;">
                    <!-- Apertura simplificada de Modales -->
                    <button type="button" onclick="document.getElementById('restaurarModalLocal').style.display='flex';" style="background: #ebf4ff; color: #4c51bf; border: 2px solid #4c51bf; padding: 10px 15px; border-radius: 6px; width: 100%; font-weight: bold; cursor: pointer; transition: 0.3s;">
                        🔄 Cambiar / Restaurar Modo Normal
                    </button>

                    <button type="button" onclick="document.getElementById('forzarModalLocal').style.display='flex';" style="background: white; color: #e53e3e; border: 2px solid #e53e3e; padding: 10px 15px; border-radius: 6px; width: 100%; font-weight: bold; cursor: pointer; transition: 0.3s;">
                        ⚠️ Forzar Estado de Emergencia
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">🧠 Análisis de Entorno (Cloud)</div>

                <div class="api-block">
                    <div class="api-title">📍 Google Maps Platform</div>
                    <p class="api-data"><span id="ui_densidad">{{ $datosApi ? $datosApi['debug_datos']['google_maps']['multiplicador_densidad'] : '-' }}</span><span style="font-size: 1.2rem; color: #718096;">x</span></p>
                    <p class="api-sub">Multiplicador de congestión en ruta.</p>
                </div>

                <div class="api-block">
                    <div class="api-title">📝 Motor PLN & Waze Live Map</div>
                    <div class="detail-row" style="border:none; margin:0;">
                        <span class="detail-label">Incidentes en la Zona</span>
                        <span class="detail-value"><span id="ui_incidentes">{{ $datosApi ? count($datosApi['debug_datos']['waze']['alertas_interceptadas']) : '-' }}</span> detectados</span>
                    </div>
                    <div class="detail-row" style="border:none; margin:0;">
                        <span class="detail-label">Severidad Aplicada</span>
                        <span class="detail-value" style="color: #e53e3e; font-size: 1.2rem;" id="ui_severidad">{{ $datosApi ? $datosApi['debug_datos']['waze']['peso_ia'] : '-' }}</span>
                    </div>

                    <div style="margin-top: 15px;">
                        <span class="detail-label">Términos NLP:</span>
                        <div id="ui_palabras" style="margin-top: 8px;">
                            @if($datosApi && count($datosApi['debug_datos']['waze']['lemas_detectados']) > 0)
                                @foreach ($datosApi['debug_datos']['waze']['lemas_detectados'] as $palabra)
                                    <span class="pln-tag">{{ $palabra }}</span>
                                @endforeach
                            @else
                                <p style="font-size: 0.8rem; color: #a0aec0; margin: 5px 0 0 0;">{{ $datosApi ? 'Flujo libre.' : 'Conexión Perdida.' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">⏱️ Ciclo Semafórico</div>

                @php
                    $modoActual = strtolower(trim($interseccion->estado_operativo));
                @endphp

                @if($modoActual === 'rojo intermitente')
                    <div class="time-box red" style="animation: pulse 1s infinite; display: flex; flex-direction: column; justify-content: center; height: 80%;">
                        <div class="time-label" style="font-size: 1.2rem;">⚠️ Modo Emergencia</div>
                        <div class="time-value" style="font-size: 2rem; margin-top: 20px;">DESTELLO ROJO</div>
                        <div class="time-unit" style="margin-top: 15px;">Alto Total Requerido</div>
                    </div>

                @elseif($modoActual === 'ambar intermitente')
                    <div class="time-box yellow" style="animation: pulse 1s infinite; display: flex; flex-direction: column; justify-content: center; height: 80%;">
                        <div class="time-label" style="font-size: 1.2rem;">⚠️ Precaución</div>
                        <div class="time-value" style="font-size: 2rem; margin-top: 20px;">DESTELLO ÁMBAR</div>
                        <div class="time-unit" style="margin-top: 15px;">Reducir Velocidad</div>
                    </div>

                @elseif($modoActual === 'apagado')
                    <div class="time-box" style="background: #4a5568; display: flex; flex-direction: column; justify-content: center; height: 80%;">
                        <div class="time-label" style="color: #cbd5e0; font-size: 1.2rem;">⚫ Mantenimiento</div>
                        <div class="time-value" style="font-size: 2.5rem; margin-top: 20px;">APAGADO</div>
                        <div class="time-unit" style="margin-top: 15px; color: #cbd5e0;">Cruce de uno y uno</div>
                    </div>

                @else
                    <div class="time-box green" id="box_verde">
                        <div class="time-label">Fase Verde</div>
                        <div class="time-value">
                            <span id="ui_verde">{{ $datosApi ? $datosApi['tiempos_calculados']['fase_verde_segundos'] :$interseccion->tiempo_verde }}</span>
                            <span class="time-unit">s</span>
                        </div>
                    </div>

                    <div class="time-box yellow">
                        <div class="time-label">Fase Amarilla</div>
                        <div class="time-value">
                            <span>{{ $interseccion->tiempo_amarillo }}</span>
                            <span class="time-unit">s</span>
                        </div>
                    </div>

                    <div class="time-box red" id="box_rojo">
                        <div class="time-label">Fase Roja</div>
                        <div class="time-value">
                            <span id="ui_rojo">{{ $datosApi ? $datosApi['tiempos_calculados']['fase_roja_segundos'] :$interseccion->tiempo_rojo }}</span>
                            <span class="time-unit">s</span>
                        </div>
                    </div>
                @endif
            </div>

        </div>


        @php
            $esAdmin = strtolower(auth()->user()->rol ?? auth()->user()->role) === 'administrador' || strtolower(auth()->user()->rol ?? auth()->user()->role) === 'admin';
        @endphp

        @if($esAdmin && isset($datosApi) && $datosApi)
        <div style="background: #1a202c; color: #00ff00; padding: 20px; border-radius: 8px; font-family: 'Courier New', Courier, monospace; font-size: 0.95rem; margin-top: 25px; border: 1px solid #2d3748; box-shadow: inset 0 0 10px rgba(0,0,0,0.5);">
            <h4 style="color: white; border-bottom: 1px dashed #4a5568; padding-bottom: 10px; margin-top: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                💻 Terminal de Diagnóstico IA - Auditoría de Algoritmos (Vista Admin)
            </h4>

            <p style="color: #63b3ed; font-weight: bold; margin-bottom: 5px; margin-top: 15px;">[1] IDENTIFICACIÓN DE VECTOR Y TRÁFICO MACROSCÓPICO...</p>
            <div style="padding-left: 15px; margin-bottom: 20px; border-left: 2px solid #4a5568;">
                > Vector Vial Exacto: <span style="color: #fbd38d; font-weight:bold;">{{ $datosApi['debug_datos']['calle_identificada'] }}</span><br>
                > Tiempo Libre (Sin Tráfico): {{ $datosApi['debug_datos']['google_maps']['tiempo_ideal_segundos'] }} segundos<br>
                > Tiempo (Congestión Actual): <span style="color: #f56565; font-weight:bold;">{{ $datosApi['debug_datos']['google_maps']['tiempo_actual_trafico'] }} segundos</span><br>
                > Densidad Vehicular (Factor D): {{ $datosApi['debug_datos']['google_maps']['multiplicador_densidad'] }}x
            </div>

            <p style="color: #63b3ed; font-weight: bold; margin-bottom: 5px;">[2] PLN Y ESCANEO SEMÁNTICO ESTRICTO...</p>
            <div style="padding-left: 15px; border-left: 2px solid #4a5568;">
                > Reportes comunitarios en {{ $datosApi['debug_datos']['calle_identificada'] }}:
                @forelse($datosApi['debug_datos']['waze']['alertas_interceptadas'] as $alerta)
                    <br>&nbsp;&nbsp; <span style="color: #fbd38d;">- {{ $alerta }}</span>
                @empty
                    <br>&nbsp;&nbsp; <span style="color: #a0aec0;">Flujo libre. Waze no reporta incidentes en ESTE tramo.</span>
                @endforelse
                <br><br>
                > Lemas Extraídos (Motor spaCy): {{ implode(', ', $datosApi['debug_datos']['waze']['lemas_detectados']) ?: 'Ninguno' }}<br>
                > Penalización Dinámica (Severidad IA): <span style="color: #48bb78; font-weight:bold;">{{ $datosApi['debug_datos']['waze']['peso_ia'] }} / 1.0</span>
            </div>
        </div>
        @endif

    </main>


    <div id="forzarModalLocal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: none; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 8px; width: 450px; max-width: 90%; border-top: 4px solid #e53e3e; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #c53030; margin: 0;">Forzar Estado - {{ $interseccion->nombre }}</h3>
                <button type="button" onclick="document.getElementById('forzarModalLocal').style.display='none'" style="cursor: pointer; font-size: 24px; color: #a0aec0; border: none; background: transparent; line-height: 1;">&times;</button>
            </div>
            <form action="{{ route('prototipos.forzar', $interseccion->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #4a5568; font-size: 0.85rem;">Seleccionar Acción</label>
                    <select name="modo_forzado" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-family: inherit;">
                        <option value="Rojo Intermitente">🔴 Rojo Intermitente (Alto Total de Emergencia)</option>
                        <option value="Ambar Intermitente">🟡 Ámbar Intermitente (Precaución)</option>
                        <option value="Apagado">⚫ Apagar Sistema (Mantenimiento)</option>
                    </select>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #4a5568; font-size: 0.85rem;">Motivo del cambio (Min. 5 letras)</label>
                    <textarea name="motivo" required minlength="5" rows="3" placeholder="Registra la justificación en bitácora..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box; font-family: inherit;"></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('forzarModalLocal').style.display='none'" style="background: #edf2f7; color: #4a5568; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">Cancelar</button>
                    <button type="submit" style="background: #e53e3e; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">Ejecutar Cambio</button>
                </div>
            </form>
        </div>
    </div>

    <div id="restaurarModalLocal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: none; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 8px; width: 450px; max-width: 90%; border-top: 4px solid #4c51bf; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #4c51bf; margin: 0;">Restaurar Operación - {{ $interseccion->nombre }}</h3>
                <button type="button" onclick="document.getElementById('restaurarModalLocal').style.display='none'" style="cursor: pointer; font-size: 24px; color: #a0aec0; border: none; background: transparent; line-height: 1;">&times;</button>
            </div>
            <form action="{{ route('prototipos.forzar', $interseccion->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #4a5568; font-size: 0.85rem;">Seleccionar Modo Estándar</label>
                    <select name="modo_forzado" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-family: inherit;">
                        <option value="Inteligente">🧠 Modo Inteligente (IA Activada)</option>
                        <option value="Fijo">⏱️ Modo Fijo (Tiempos Base)</option>
                        <option value="Mantenimiento">🔧 Modo Mantenimiento</option>
                    </select>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #4a5568; font-size: 0.85rem;">Motivo de la restauración (Min. 5 letras)</label>
                    <textarea name="motivo" required minlength="5" rows="3" placeholder="Ej. Emergencia concluida, fin de mantenimiento preventivo..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box; font-family: inherit;"></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('restaurarModalLocal').style.display='none'" style="background: #edf2f7; color: #4a5568; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">Cancelar</button>
                    <button type="submit" style="background: #4c51bf; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">Restaurar Estado</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.estadoOperativo = "{{ strtolower(trim($interseccion->estado_operativo)) }}";
        window.interseccionId = "{{ $interseccion->id }}";
        window.isCurrentlyOnline = {{ $datosApi ? 'true' : 'false' }};

        @if(isset($datosApi) && $datosApi)
            const debugData = @json($datosApi['debug_datos']);
            console.groupCollapsed('%c🔍 Auditoría de IA: ' + debugData.calle_identificada, 'color: #38a169; font-weight: bold; font-size: 12px;');

            console.log('%c📍 GOOGLE MAPS PLATFORM', 'color: #3182ce; font-weight: bold;');
            console.table({
                "Tiempo Ideal (Sin Tráfico)": debugData.google_maps.tiempo_ideal_segundos + " s",
                "Tiempo Actual (Con Tráfico)": debugData.google_maps.tiempo_actual_trafico + " s",
                "Multiplicador de Densidad": debugData.google_maps.multiplicador_densidad + "x"
            });

            console.log('%c📝 MOTOR PLN & WAZE', 'color: #dd6b20; font-weight: bold;');
            console.log("Alertas Crudas Interceptadas:", debugData.waze.alertas_interceptadas.length > 0 ? debugData.waze.alertas_interceptadas : "Ninguna");
            console.log("Lemas Extraídos (spaCy):", debugData.waze.lemas_detectados.length > 0 ? debugData.waze.lemas_detectados : "Ninguno");
            console.log("Penalización de Severidad Aplicada:", debugData.waze.peso_ia);

            console.groupEnd();
        @endif
    </script>
    <script src="{{ asset('./JS/Monitor.js') }}"></script>
</body>
</html>