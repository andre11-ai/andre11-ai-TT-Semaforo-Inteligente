import os
import warnings
import joblib
import numpy as np
import pandas as pd
from sklearn.neural_network import MLPRegressor
from sklearn.preprocessing import StandardScaler
from sklearn.pipeline import Pipeline
from sklearn.metrics import mean_absolute_error, r2_score

warnings.filterwarnings("ignore")

FEATURES_MODELO = [
    "speed_lag_1", "speed_lag_2", "speed_lag_3", "speed_lag_4",
    "tt_lag_1", "tt_lag_2", "tt_lag_3", "tt_lag_4",
    "jams_lag_1", "jams_lag_2", "jams_lag_3", "jams_lag_4",
    "speed_ema_4", "speed_diff_1_4",
    "free_flow_speed", "free_flow_travel_time", "v_libre_kmh",
    "temperature_c", "precipitation_hourly_total_mm", "wind_speed_ms",
    "segment_length_m", "functional_road_class", "signalized_locations_count",
    "education_poi_count_100m", "health_poi_count_100m",
    "TrafficIndexLive", "JamsCount", "JamsDelay",
    "hora_sin", "hora_cos", "dia_semana", "es_fin_semana"
]

def entrenar():
    print("1. Cargando dataset_IA_CDMX_listo1.csv y dataset_IA_CDMX_listo2.csv...")
    df1 = pd.read_csv("data/dataset_IA_CDMX_listo1.csv")
    df2 = pd.read_csv("data/dataset_IA_CDMX_listo2.csv")
    df = pd.concat([df1, df2], ignore_index=True)

    df["dt"] = pd.to_datetime(df["datetime"])
    df = df.sort_values(["segment_id", "dt"]).reset_index(drop=True)

    v_libre_map = df.groupby("segment_id")["current_speed"].quantile(0.95).rename("v_libre_kmh")
    df = df.merge(v_libre_map, on="segment_id", how="left")
    df["multiplicador_densidad"] = (df["v_libre_kmh"] / df["current_speed"].clip(lower=1.0)).clip(lower=0.5, upper=4.0)

    hora_float = df["dt"].dt.hour + df["dt"].dt.minute / 60.0
    df["hora_sin"] = np.sin(2.0 * np.pi * hora_float / 24.0)
    df["hora_cos"] = np.cos(2.0 * np.pi * hora_float / 24.0)
    df["dia_semana"] = df["dt"].dt.dayofweek / 6.0
    df["es_fin_semana"] = (df["dt"].dt.dayofweek >= 5).astype(float)

    for lag in [1, 2, 3, 4]:
        df[f"speed_lag_{lag}"] = df.groupby("segment_id")["current_speed"].shift(lag)
        df[f"tt_lag_{lag}"] = df.groupby("segment_id")["current_travel_time"].shift(lag)
        df[f"jams_lag_{lag}"] = df.groupby("segment_id")["JamsDelay"].shift(lag)

    df["speed_ema_4"] = df.groupby("segment_id")["speed_lag_1"].transform(lambda s: s.ewm(span=4).mean())
    df["speed_diff_1_4"] = df["speed_lag_1"] - df["speed_lag_4"]

    df_clean = df.dropna().reset_index(drop=True)
    os.makedirs("models", exist_ok=True)
    ruta_modelo = "models/modelo_semaforo_ia.joblib"

    if os.path.exists(ruta_modelo):
        print(f"2. Modelo previo detectado en {ruta_modelo}, cargando...")
        paquete = joblib.load(ruta_modelo)
        pipeline_ia = paquete["pipeline"]
    else:
        fecha_corte = pd.Timestamp("2025-02-05 00:00:00")
        train_df = df_clean[df_clean["dt"] < fecha_corte]
        test_df = df_clean[df_clean["dt"] >= fecha_corte]

        print(f"2. Entrenando Red Neuronal con {len(train_df):,} registros...")
        pipeline_ia = Pipeline([
            ("scaler", StandardScaler()),
            ("mlp", MLPRegressor(
                hidden_layer_sizes=(64, 32),
                activation="relu",
                solver="adam",
                max_iter=80,
                random_state=42,
                early_stopping=True
            ))
        ])

        pipeline_ia.fit(train_df[FEATURES_MODELO], train_df["multiplicador_densidad"])

        preds_d = np.clip(pipeline_ia.predict(test_df[FEATURES_MODELO]), 0.5, 4.0)
        verde_real = np.clip(np.floor(45 + 15 * (test_df["multiplicador_densidad"] - 1.0)), 15, 90)
        verde_pred = np.clip(np.floor(45 + 15 * (preds_d - 1.0)), 15, 90)

        print(f"   -> MAE Multiplicador D: {mean_absolute_error(test_df['multiplicador_densidad'], preds_d):.4f}")
        print(f"   -> R2 Score           : {r2_score(test_df['multiplicador_densidad'], preds_d):.4f}")
        print(f"   -> MAE Fase Verde (s) : {mean_absolute_error(verde_real, verde_pred):.2f} segundos")

        joblib.dump({"pipeline": pipeline_ia, "features": FEATURES_MODELO}, ruta_modelo)

    df_clean["hora_int"] = df_clean["dt"].dt.hour
    llaves_grupo = ["segment_id", "es_fin_semana", "hora_int"]
    cols_metricas = [c for c in FEATURES_MODELO + ["current_speed"] if c not in llaves_grupo]

    perfiles = df_clean.groupby(llaves_grupo, as_index=False)[cols_metricas].median()
    perfiles.to_parquet("models/perfiles_segmentos.parquet", index=False)
    print("3. Archivos exportados exitosamente en models/modelo_semaforo_ia.joblib y models/perfiles_segmentos.parquet")

if __name__ == "__main__":
    entrenar()