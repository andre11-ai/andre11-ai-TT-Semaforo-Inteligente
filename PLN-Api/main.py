from fastapi import FastAPI
from pydantic import BaseModel
import spacy
import mysql.connector
import requests
import math
import unicodedata

app = FastAPI(title="Motor PLN - Semáforo Inteligente TT")
nlp = spacy.load("es_core_news_sm")

GOOGLE_MAPS_API_KEY = "" 

class PayloadSemaforo(BaseModel):
    interseccion_id: str
    lat_semaforo: float
    lon_semaforo: float
    lat_destino: float 
    lon_destino: float 
    calle_principal: str = ""

@app.get("/")
def health_check():
    return {"status": "ok", "mensaje": "Motor IA en línea"}

def obtener_diccionario_bd():
    diccionario_dinamico = {}
    conexion = None
    cursor = None
    try:
        conexion = mysql.connector.connect(host='127.0.0.1', user='root', password='', database='TT-Semaforo')
        cursor = conexion.cursor(dictionary=True)
        cursor.execute("SELECT palabra, peso FROM pln_diccionarios")
        for fila in cursor.fetchall():
            diccionario_dinamico[fila['palabra'].lower()] = float(fila['peso']) / 10.0
    except Exception as e:
        print(f"Error BD: {e}") 
    finally:
        if cursor: cursor.close()
        if conexion: conexion.close()
        
    return diccionario_dinamico

def obtener_nombre_calle_google(lat_origen: float, lon_origen: float, lat_dest: float, lon_dest: float) -> str:
    mid_lat = (lat_origen + lat_dest) / 2.0
    mid_lon = (lon_origen + lon_dest) / 2.0
    try:
        url = f"https://maps.googleapis.com/maps/api/geocode/json?latlng={mid_lat},{mid_lon}&key={GOOGLE_MAPS_API_KEY}"
        res = requests.get(url, timeout=3).json()
        if res.get("status") == "OK":
            for result in res["results"]:
                for component in result["address_components"]:
                    if "route" in component["types"]:
                        return component["long_name"]
            return res["results"][0]["address_components"][1]["long_name"] 
    except Exception as e:
        print(f"Error Geocoding: {e}")
    return "Avenida Desconocida"

def calcular_sentido(lat1: float, lon1: float, lat2: float, lon2: float) -> str:
    lat1, lon1, lat2, lon2 = map(math.radians, [lat1, lon1, lat2, lon2])
    dlon = lon2 - lon1
    y = math.sin(dlon) * math.cos(lat2)
    x = math.cos(lat1) * math.sin(lat2) - math.sin(lat1) * math.cos(lat2) * math.cos(dlon)
    brng = math.degrees(math.atan2(y, x))
    brng = (brng + 360) % 360
    
    if brng >= 315 or brng < 45: return "Norte"
    elif 45 <= brng < 135: return "Este"
    elif 135 <= brng < 225: return "Sur"
    else: return "Oeste"

def obtener_densidad_google(origen: str, destino: str):
    url = "https://maps.googleapis.com/maps/api/distancematrix/json"
    params = {"origins": origen, "destinations": destino, "departure_time": "now", "key": GOOGLE_MAPS_API_KEY}
    tiempo_ideal = 0; tiempo_trafico = 0; densidad = 1.0; msg_debug = "OK"
    try:
        respuesta = requests.get(url, params=params, timeout=3).json() 
        
        if respuesta.get("status") != "OK":
            return densidad, tiempo_ideal, tiempo_trafico, f"API Error: {respuesta.get('status')}"
            
        elemento = respuesta["rows"][0]["elements"][0]
        if elemento.get("status") == "OK" and "duration_in_traffic" in elemento:
            tiempo_ideal = elemento["duration"]["value"]
            tiempo_trafico = elemento["duration_in_traffic"]["value"]
            densidad = round(tiempo_trafico / tiempo_ideal, 2)
        else:
            msg_debug = f"Status Tramo: {elemento.get('status')}"
    except Exception as e: 
        msg_debug = f"Error Distance Matrix: {str(e)}"
        
    return densidad, tiempo_ideal, tiempo_trafico, msg_debug

def limpiar_texto(texto: str) -> str:

    return ''.join(c for c in unicodedata.normalize('NFD', texto) if unicodedata.category(c) != 'Mn').lower()

def coinciden_calles(calle_waze: str, calle_google: str) -> bool:
    if not calle_waze or not calle_google: return False
    exclusiones = {"avenida", "av.", "av", "calle", "boulevard", "blvd", "calzada", "eje", "de", "los", "las", "el", "la"}
    
    tokens_waze = set([w for w in limpiar_texto(calle_waze).split() if w not in exclusiones and len(w) > 3])
    tokens_google = set([w for w in limpiar_texto(calle_google).split() if w not in exclusiones and len(w) > 3])
    
    return len(tokens_waze.intersection(tokens_google)) > 0

def obtener_alertas_waze_real(lat: float, lon: float, calle_exacta: str):
    delta = 0.5 / 111.0 
    url = f"https://www.waze.com/row-rtserver/web/TGeoRSS?bottom={lat-delta}&top={lat+delta}&left={lon-delta}&right={lon+delta}&env=row&types=alerts"
    headers = {"User-Agent": "Mozilla/5.0"}
    
    textos_extraidos = []
    alertas_crudas = []
    traductor = {"accident": "choque", "jam": "trafico", "hazard": "peligro", "flood": "inundacion", "road_closed": "bloqueo"}

    try:
        respuesta = requests.get(url, headers=headers, timeout=5)
        if respuesta.status_code == 200:
            datos = respuesta.json()
            if "alerts" in datos:
                for alerta in datos["alerts"]:
                    calle_waze = alerta.get("street", "")
                    if not coinciden_calles(calle_waze, calle_exacta): continue 
                        
                    codigo_waze = (alerta.get("type", "") + " " + alerta.get("subtype", "")).lower()
                    texto = ""
                    for eng, esp in traductor.items():
                        if eng in codigo_waze: texto += esp + " "
                    
                    if texto:
                        textos_extraidos.append(texto.strip())
                        alertas_crudas.append(f"[{calle_waze.title()}] {alerta.get('reportDescription', codigo_waze)}")
    except Exception as e:
        print(f"Error Waze: {e}")
    return textos_extraidos, alertas_crudas


@app.post("/api/pln/procesar")
def procesar_alerta(payload: PayloadSemaforo):
    calle_real = obtener_nombre_calle_google(payload.lat_semaforo, payload.lon_semaforo, payload.lat_destino, payload.lon_destino)
    sentido_real = calcular_sentido(payload.lat_semaforo, payload.lon_semaforo, payload.lat_destino, payload.lon_destino)
    
    diccionario_actual = obtener_diccionario_bd()
    textos_waze, alertas_visuales = obtener_alertas_waze_real(payload.lat_semaforo, payload.lon_semaforo, calle_real)
    texto_combinado = " ".join(textos_waze)
    
    doc = nlp(texto_combinado)
    lemas_extraidos = [token.lemma_ for token in doc if token.pos_ in ["NOUN", "ADJ", "VERB"] and not token.is_stop]
    pesos = [diccionario_actual[l] for l in lemas_extraidos if l in diccionario_actual]
    severidad_pln = max(pesos) if pesos else 0.0
    
    origen_google = f"{payload.lat_semaforo},{payload.lon_semaforo}"
    destino_google = f"{payload.lat_destino},{payload.lon_destino}"
    densidad_real, t_ideal, t_trafico, status_google = obtener_densidad_google(origen_google, destino_google)
    
    verde = max(15, min(90, int(45 + (severidad_pln * 25) + ((densidad_real - 1.0) * 15))))
    rojo = max(15, min(90, 105 - verde))
    
    return {
        "interseccion": payload.interseccion_id,
        "debug_datos": {
            "calle_identificada": f"{calle_real} (Sentido: {sentido_real})",
            "google_maps": {
                "tiempo_ideal_segundos": t_ideal,
                "tiempo_actual_trafico": t_trafico,
                "multiplicador_densidad": densidad_real,
                "estado_api": status_google
            },
            "waze": {
                "alertas_interceptadas": alertas_visuales,
                "lemas_detectados": list(set(lemas_extraidos)),
                "peso_ia": severidad_pln
            }
        },
        "tiempos_calculados": {
            "fase_verde_segundos": verde,
            "fase_roja_segundos": rojo
        }
    }