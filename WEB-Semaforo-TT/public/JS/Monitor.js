document.addEventListener("DOMContentLoaded", function() {
    console.log("[System] Inicializando módulo de telemetría en vivo...");

    const estadoOperativo = window.estadoOperativo || "";
    const interseccionId = window.interseccionId;
    const isCurrentlyOnline = window.isCurrentlyOnline;

    console.log("[Config] Modo operativo de intersección predeterminado:", estadoOperativo.toUpperCase());
    console.log("[Network] Estatus inicial de conexión al microservicio:", isCurrentlyOnline ? "ONLINE" : "OFFLINE");

    if (estadoOperativo.includes('inteligente')) {

        console.log("[Sync] Ciclo de actualización de datos IA configurado a 3600000 ms (1 Hora).");
        console.log("[Watchdog] Sistema Failsafe activado. Intervalo de sondeo (Heartbeat): 5000 ms.");

        setInterval(() => {
            fetch('/api/ping-python')
                .then(response => response.json())
                .then(data => {
                    console.log("[Heartbeat] Ping al microservicio completado. Estatus de respuesta:", data.status.toUpperCase());

                    if (data.status === 'error' && isCurrentlyOnline) {
                        console.error("[Failsafe] ERROR CRÍTICO: Pérdida de conexión con el motor IA. Forzando recarga de seguridad...");
                        window.location.reload();
                    }
                    else if (data.status === 'ok' && !isCurrentlyOnline) {
                        console.warn("🟢 [Failsafe] RESOLUCIÓN: Conexión con el motor IA restablecida. Restaurando telemetría...");
                        window.location.reload();
                    }
                })
                .catch(err => {
                    console.error("🟢 [Watchdog] Advertencia: Intentando reconexión con el nodo del microservicio...");
                    if (isCurrentlyOnline) window.location.reload();
                });
        }, 5000);

        if (isCurrentlyOnline) {
            setInterval(() => {
                console.log("[API] Solicitando nuevo ciclo de evaluación PLN y Tráfico (1 Hora)...");

                fetch(`/api/monitor/datos/${interseccionId}`)
                    .then(response => response.json())
                    .then(res => {
                        if (res.status === 'success') {
                            console.log("[API] Paquete de datos procesado e inyectado exitosamente en la UI.");
                            const datos = res.data;

                            const densidad = datos.debug_datos
                                ? datos.debug_datos.google_maps.multiplicador_densidad
                                : datos.google_maps.densidad_trafico_actual;

                            const incidentes = datos.debug_datos
                                ? datos.debug_datos.waze.alertas_interceptadas.length
                                : datos.waze.incidentes_activos_zona;

                            const severidad = datos.debug_datos
                                ? datos.debug_datos.waze.peso_ia
                                : datos.pln.peso_ia_aplicado;

                            const palabras = datos.debug_datos
                                ? datos.debug_datos.waze.lemas_detectados
                                : datos.pln.coincidencias_db;

                            document.getElementById('ui_densidad').innerText = densidad;
                            document.getElementById('ui_incidentes').innerText = incidentes;
                            document.getElementById('ui_severidad').innerText = severidad;

                            const contenedorPalabras = document.getElementById('ui_palabras');
                            contenedorPalabras.innerHTML = '';
                            if (palabras && palabras.length > 0) {
                                palabras.forEach(palabra => {
                                    contenedorPalabras.innerHTML += `<span class="pln-tag">${palabra}</span>`;
                                });
                            } else {
                                contenedorPalabras.innerHTML = '<p style="font-size: 0.8rem; color: #a0aec0; margin: 5px 0 0 0;">Flujo libre.</p>';
                            }

                            const uiVerde = document.getElementById('ui_verde');
                            const uiRojo = document.getElementById('ui_rojo');

                            if (uiVerde.innerText !== String(datos.tiempos_calculados.fase_verde_segundos)) {
                                document.getElementById('box_verde').style.transform = 'scale(1.05)';
                                document.getElementById('box_rojo').style.transform = 'scale(1.05)';
                                setTimeout(() => {
                                    uiVerde.innerText = datos.tiempos_calculados.fase_verde_segundos;
                                    uiRojo.innerText = datos.tiempos_calculados.fase_roja_segundos;
                                    document.getElementById('box_verde').style.transform = 'scale(1)';
                                    document.getElementById('box_rojo').style.transform = 'scale(1)';
                                }, 300);
                            }
                        }
                    });
            }, 3600000);
        }
    }
});
