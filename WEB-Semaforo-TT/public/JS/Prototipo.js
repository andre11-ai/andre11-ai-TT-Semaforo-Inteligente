const map = L.map('map').setView([19.4326, -99.1332], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

let originMarker = null;
let destMarker = null;
let routeLine = null;

const semaforosExistentes = window.semaforosExistentes || [];

const greenIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', iconSize: [25, 41], iconAnchor: [12, 41] });
const redIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', iconSize: [25, 41], iconAnchor: [12, 41] });
const greyIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png', iconSize: [25, 41], iconAnchor: [12, 41] });

semaforosExistentes.forEach(s => {
    L.marker([s.latitud, s.longitud], {icon: greyIcon}).addTo(map).bindPopup(`<b>${s.nombre}</b>`);
    if(s.latitud_destino && s.longitud_destino) {
        L.polyline([[s.latitud, s.longitud], [s.latitud_destino, s.longitud_destino]], {color: '#a0aec0', weight: 4, dashArray: '5, 5'}).addTo(map);
    }
});

map.on('click', function(e) {
    const lat = e.latlng.lat.toFixed(6);
    const lng = e.latlng.lng.toFixed(6);

    if (!originMarker) {
        originMarker = L.marker([lat, lng], {icon: greenIcon}).addTo(map).bindPopup("Semáforo (Origen)").openPopup();
        document.getElementById('input_lat').value = lat;
        document.getElementById('input_lng').value = lng;
    } else if (!destMarker) {
        destMarker = L.marker([lat, lng], {icon: redIcon}).addTo(map).bindPopup("Fin del Tramo (Destino)").openPopup();
        document.getElementById('input_lat_destino').value = lat;
        document.getElementById('input_lng_destino').value = lng;

        routeLine = L.polyline([[originMarker.getLatLng().lat, originMarker.getLatLng().lng], [lat, lng]], {
            color: '#3b5998', weight: 5, opacity: 0.8
        }).addTo(map);
    } else {
        map.removeLayer(originMarker);
        map.removeLayer(destMarker);
        if(routeLine) map.removeLayer(routeLine);

        originMarker = null; destMarker = null; routeLine = null;
        document.getElementById('input_lat').value = ''; document.getElementById('input_lng').value = '';
        document.getElementById('input_lat_destino').value = ''; document.getElementById('input_lng_destino').value = '';
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
