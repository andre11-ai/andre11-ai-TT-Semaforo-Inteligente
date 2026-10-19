from fastapi import FastAPI
from pydantic import BaseModel
from datetime import datetime
import numpy as np
import pandas as pd
import joblib
from sentence_transformers import SentenceTransformer, util
from config_intersecciones import (
    CATALOGO_APROXIMACIONES,
    buscar_aproximacion_mas_cercana,
    calcular_distancia_y_sentido
)

app = FastAPI(title="Motor IA y PLN con Datos Crudos - Semáforo Inteligente TT")

paquete_ia = joblib.load("models/modelo_semaforo_ia.joblib")
modelo_numerico = paquete_ia["pipeline"]
FEATURES_MODELO = paquete_ia["features"]
perfiles_df = pd.read_parquet("models/perfiles_segmentos.parquet")

modelo_nlp = SentenceTransformer("paraphrase-multilingual-MiniLM-L12-v2")
ANCLAS_SEMANTICAS = {
    "grave": "Accidente vial grave, choque aparatoso, volcadura, atropellado, ambulancia, bloqueo total o calle cerrada",
    "moderado": "Tráfico pesado, vehículo descompuesto, lluvia intensa, encharcamiento, obras viales o marcha lenta",
    "libre": "Vialidad libre, tránsito fluido, sin incidentes, circulación normal sin percances"
}
EMBEDDINGS_ANCLAS = {k: modelo_nlp.encode(v, convert_to_tensor=True) for k, v in ANCLAS_SEMANTICAS.items()}


class PayloadSemaforoCSV(BaseModel):
    interseccion_id: str = "INS_COSME_1"
    lat_semaforo: float = 19.440513
    lon_semaforo: float = -99.157670
    lat_destino: float = 19.439828
    lon_destino: float = -99.155911
    fecha_hora_simulacion: str = ""     
    reporte_incidente_texto: str = ""    
    lluvia_mm_override: float = -1.0     
    modo_operacion: str = "Inteligente"  


def evaluar_severidad_minilm(texto_reporte: str) -> float:
    if not texto_reporte or len(texto_reporte.strip()) < 3:
        return 0.0
    emb_texto = modelo_nlp.encode(texto_reporte, convert_to_tensor=True)
    sim_grave = float(util.cos_sim(emb_texto, EMBEDDINGS_ANCLAS["grave"])[0][0])
    sim_mod = float(util.cos_sim(emb_texto, EMBEDDINGS_ANCLAS["moderado"])[0][0])
    sim_libre = float(util.cos_sim(emb_texto, EMBEDDINGS_ANCLAS["libre"])[0][0])

    if sim_libre >= max(sim_grave, sim_mod):
        return 0.0
    if sim_grave >= sim_mod:
        return round(float(np.clip(sim_grave, 0.55, 1.0)), 2)
    return round(float(np.clip(sim_mod * 0.65, 0.15, 0.55)), 2)


@app.get("/")
def health_check():
    return {"status": "ok", "mensaje": "Motor IA (LSTM-MLP + MiniLM) en línea sin APIs externas"}


@app.post("/api/pln/procesar")
def procesar_semaforo_csv(payload: PayloadSemaforoCSV):
    if payload.modo_operacion == "Forzado_Rojo":
        return {
            "interseccion": payload.interseccion_id,
            "modo_activo": "Forzado_Rojo (RN10)",
            "tiempos_calculados": {
                "fase_verde_segundos": 0,
                "fase_amarilla_segundos": 0,
                "fase_roja_segundos": 90
            }
        }

    if payload.modo_operacion == "Offline":
        return {
            "interseccion": payload.interseccion_id,
            "modo_activo": "Offline_Respaldo (RN04)",
            "tiempos_calculados": {
                "fase_verde_segundos": 45,
                "fase_amarilla_segundos": 4,
                "fase_roja_segundos": 56
            }
        }

    if payload.interseccion_id in CATALOGO_APROXIMACIONES:
        clave_aprox = payload.interseccion_id
        info_aprox = CATALOGO_APROXIMACIONES[clave_aprox]
    else:
        clave_aprox, info_aprox = buscar_aproximacion_mas_cercana(
            payload.lat_semaforo, payload.lon_semaforo, payload.lat_destino, payload.lon_destino
        )

    dist_m, azimut, sentido_calc = calcular_distancia_y_sentido(
        payload.lat_semaforo, payload.lon_semaforo, payload.lat_destino, payload.lon_destino
    )
    seg_id = info_aprox["segment_id"]

    dt_consulta = pd.to_datetime(payload.fecha_hora_simulacion) if payload.fecha_hora_simulacion else datetime.now()
    es_finde = float(dt_consulta.weekday() >= 5)
    hora_int = dt_consulta.hour

    fila_csv = perfiles_df[
        (perfiles_df["segment_id"] == seg_id) &
        (perfiles_df["es_fin_semana"] == es_finde) &
        (perfiles_df["hora_int"] == hora_int)
    ].copy()

    if payload.lluvia_mm_override >= 0.0:
        fila_csv["precipitation_hourly_total_mm"] = min(1.0, payload.lluvia_mm_override / 20.0)

    X_input = fila_csv[FEATURES_MODELO]
    densidad_predicha = round(float(np.clip(modelo_numerico.predict(X_input)[0], 0.5, 4.0)), 2)
    vel_actual_kmh = float(fila_csv["current_speed"].iloc[0])
    vel_libre_kmh = float(fila_csv["v_libre_kmh"].iloc[0])

    t_ideal_seg = int(round((dist_m / 1000.0) / max(vel_libre_kmh, 5.0) * 3600.0))
    t_trafico_seg = int(round(t_ideal_seg * densidad_predicha))

    severidad_pln = evaluar_severidad_minilm(payload.reporte_incidente_texto)

    verde_bruto = 45.0 + (severidad_pln * 25.0) + ((densidad_predicha - 1.0) * 15.0)
    verde = max(15, min(90, int(round(verde_bruto))))

    amarillo_bruto = 1.0 + (vel_actual_kmh / 24.48)
    amarillo = max(3, min(6, int(round(amarillo_bruto))))

    rojo_bruto = 105 - (verde + amarillo)
    rojo = max(15, min(90, int(rojo_bruto)))

    return {
        "interseccion": clave_aprox,
        "debug_datos": {
            "calle_identificada": f"{info_aprox['interseccion']} -> {info_aprox['direccion']} (Sentido: {sentido_calc})",
            "segment_id_csv": seg_id,
            "distancia_tramo_metros": dist_m,
            "motor_numerico_csv": {
                "velocidad_actual_kmh": round(vel_actual_kmh, 2),
                "velocidad_flujo_libre_kmh": round(vel_libre_kmh, 2),
                "tiempo_ideal_segundos": t_ideal_seg,
                "tiempo_actual_trafico": t_trafico_seg,
                "multiplicador_densidad": densidad_predicha,
                "estado_motor": "OK (Red Neuronal Local CSV)"
            },
            "motor_semantico_minilm": {
                "reporte_recibido": payload.reporte_incidente_texto,
                "peso_ia": severidad_pln
            }
        },
        "tiempos_calculados": {
            "fase_verde_segundos": verde,
            "fase_amarilla_segundos": amarillo,
            "fase_roja_segundos": rojo
        }
    }