<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nuevo Prototipo - Sistema de Tráfico</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        body { background-color: #f8fafc; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-dark { background-color: #1a202c; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header-title { font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .btn-volver { background-color: transparent; color: white; border: 1px solid #4a5568; padding: 6px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; transition: 0.2s; }
        .btn-volver:hover { background-color: #2d3748; }

        .main-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }

        .grid-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .section-title { font-size: 1.2rem; color: #2d3748; margin-top: 0; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #edf2f7; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; color: #4a5568; margin-bottom: 8px; font-size: 0.9rem; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box; font-family: inherit; font-size: 0.95rem; }
        .form-help { font-size: 0.8rem; color: #a0aec0; margin-top: 5px; display: block; }

        .times-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .times-grid label { font-size: 0.85rem; }
        .label-green { color: #38a169 !important; }
        .label-yellow { color: #d69e2e !important; }
        .label-red { color: #e53e3e !important; }

        #map { height: 350px; width: 100%; border-radius: 8px; border: 1px solid #cbd5e0; box-shadow: inset 0 0 5px rgba(0,0,0,0.1); margin-bottom: 15px;}

        .btn-submit { background-color: #3b5998; color: white; border: none; padding: 12px 20px; width: 100%; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 20px; }
        .btn-submit:hover { background-color: #2a406d; }
    </style>
</head>
<body>

    <nav class="header-dark">
        <div class="header-title">
            <span>🚦 Sistema de Tráfico</span>
            <span style="color: #4a5568;">|</span>
            <span style="color: #a0aec0; font-weight: 400;">Registrar Nuevo Prototipo</span>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-volver">⬅ Volver</a>
    </nav>

    <main class="main-container">
        <div class="card">
            <form action="{{ route('prototipos.store') }}" method="POST">
                @csrf
                <div class="grid-layout">

                    <div>
                        <h2 class="section-title">Datos del Hardware</h2>

                        <div class="form-group">
                            <label>Nombre del Cruce / Intersección</label>
                            <input type="text" id="input_nombre" name="nombre" required placeholder="Ej. Eje Central y Madero">
                            <span class="form-help">Escribe la calle y presiona fuera de la caja para buscar en el mapa.</span>
                        </div>

                        <div class="form-group">
                            <label>Dirección IP del Controlador</label>
                            <input type="text" name="ip_controlador" value="{{ $ipSugerida ?? '' }}" required placeholder="192.168.1.51">
                        </div>

                        <div class="form-group" style="margin-top: 30px;">
                            <label style="font-size: 1.1rem; border-bottom: 1px solid #edf2f7; padding-bottom: 5px; margin-bottom: 15px;">Tiempos Base (Failsafe)</label>
                            <span class="form-help" style="margin-bottom: 15px;">Tiempos de rescate en caso de que la IA o el internet fallen.</span>

                            <div class="times-grid">
                                <div>
                                    <label class="label-green">Verde (s)</label>
                                    <input type="number" name="tiempo_verde" min="15" max="90" value="45" required>
                                </div>
                                <div>
                                    <label class="label-yellow">Amarillo (s)</label>
                                    <input type="number" name="tiempo_amarillo" min="3" max="6" value="4" required>
                                </div>
                                <div>
                                    <label class="label-red">Rojo (s)</label>
                                    <input type="number" name="tiempo_rojo" min="15" max="90" value="45" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 25px;">
                            <label>Estado Inicial</label>
                            <select name="estado_operativo" required>
                                <option value="Inteligente">Modo Inteligente (IA)</option>
                                <option value="Fijo">Modo Fijo</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <h2 class="section-title">Ubicación Geográfica y Vector Vial</h2>
                        <span class="form-help" style="margin-bottom: 10px;">
                            <strong>Paso 1:</strong> Haz clic para colocar el semáforo (Verde).<br>
                            <strong>Paso 2:</strong> Haz clic al final de la avenida (Rojo) para trazar la ruta de la IA.
                        </span>

                        <div id="map"></div>

                        <div style="background: #f7fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 15px;">
                            <p style="margin: 0 0 10px 0; font-weight: bold; color: #4a5568; font-size: 0.9rem;">📍 Coordenadas del Vector Vial</p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <label style="font-size: 0.8rem; color: #718096; display:block; margin-bottom:3px;">Latitud (Origen)</label>
                                    <input type="text" id="input_lat" name="latitud" readonly required style="width:100%; padding:8px; border:1px solid #cbd5e0; border-radius:4px; background:#edf2f7; box-sizing:border-box;">
                                </div>
                                <div>
                                    <label style="font-size: 0.8rem; color: #718096; display:block; margin-bottom:3px;">Longitud (Origen)</label>
                                    <input type="text" id="input_lng" name="longitud" readonly required style="width:100%; padding:8px; border:1px solid #cbd5e0; border-radius:4px; background:#edf2f7; box-sizing:border-box;">
                                </div>
                                <div>
                                    <label style="font-size: 0.8rem; color: #718096; display:block; margin-bottom:3px;">Latitud (Destino)</label>
                                    <input type="text" id="input_lat_destino" name="latitud_destino" readonly required style="width:100%; padding:8px; border:1px solid #cbd5e0; border-radius:4px; background:#edf2f7; box-sizing:border-box;">
                                </div>
                                <div>
                                    <label style="font-size: 0.8rem; color: #718096; display:block; margin-bottom:3px;">Longitud (Destino)</label>
                                    <input type="text" id="input_lng_destino" name="longitud_destino" readonly required style="width:100%; padding:8px; border:1px solid #cbd5e0; border-radius:4px; background:#edf2f7; box-sizing:border-box;">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">💾 Guardar Prototipo</button>
                    </div>

                </div>
            </form>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const semaforosExistentes = @json($intersecciones ?? []);

        const map = L.map('map').setView([19.4326, -99.1332], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        let originMarker = null;
        let destMarker = null;
        let routeLine = null;

        const greenIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', iconSize: [25, 41], iconAnchor: [12, 41] });
        const redIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', iconSize: [25, 41], iconAnchor: [12, 41] });
        const greyIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png', iconSize: [25, 41], iconAnchor: [12, 41] });

        semaforosExistentes.forEach(s => {
            L.marker([s.latitud, s.longitud], {icon: greyIcon}).addTo(map).bindPopup(`<b>${s.nombre}</b>`);
            // PROTECCIÓN CONTRA VALORES CERO DE LA BASE DE DATOS VIEJA
            if(s.latitud_destino != null && s.latitud_destino != 0) {
                L.polyline([
                    [s.latitud, s.longitud],
                    [s.latitud_destino, s.longitud_destino]
                ], {color: '#a0aec0', weight: 4, dashArray: '5, 5'}).addTo(map);
            }
        });

        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);

            if (!originMarker) {
                originMarker = L.marker([lat, lng], {icon: greenIcon}).addTo(map).bindPopup("Origen").openPopup();
                document.getElementById('input_lat').value = lat;
                document.getElementById('input_lng').value = lng;
            } else if (!destMarker) {
                destMarker = L.marker([lat, lng], {icon: redIcon}).addTo(map).bindPopup("Destino").openPopup();
                document.getElementById('input_lat_destino').value = lat;
                document.getElementById('input_lng_destino').value = lng;

                routeLine = L.polyline([
                    [originMarker.getLatLng().lat, originMarker.getLatLng().lng],
                    [lat, lng]
                ], {
                    color: '#3b5998', weight: 5, opacity: 0.8, dashArray: '10, 10'
                }).addTo(map);
            } else {
                map.removeLayer(originMarker);
                map.removeLayer(destMarker);
                map.removeLayer(routeLine);
                originMarker = null; destMarker = null; routeLine = null;

                document.getElementById('input_lat').value = '';
                document.getElementById('input_lng').value = '';
                document.getElementById('input_lat_destino').value = '';
                document.getElementById('input_lng_destino').value = '';
            }
        });

        document.getElementById('input_nombre').addEventListener('change', function() {
            if (this.value.length > 4) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.value + ', Ciudad de Mexico')}`)
                .then(res => res.json()).then(data => {
                    if (data.length > 0) { map.setView([data[0].lat, data[0].lon], 17); }
                });
            }
        });
    </script>
</body>
</html>
