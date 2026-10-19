import math

CATALOGO_APROXIMACIONES = {
    "INS_COSME_1": {
        "interseccion": "AV INSURGENTES CENTRO - AV. RIBERA DE SAN COSME",
        "direccion": "Av. Ribera San Cosme dir. Buenavista / Metro Insurgentes",
        "origen": (19.440513, -99.157670),
        "destino": (19.439828, -99.155911),
        "segment_id": 1
    },
    "INS_COSME_2": {
        "interseccion": "AV INSURGENTES CENTRO - AV. RIBERA DE SAN COSME",
        "direccion": "Av. Ribera San Cosme dir. Metro San Cosme / C. Antonio del Castillo",
        "origen": (19.439013, -99.153579),
        "destino": (19.439782, -99.155459),
        "segment_id": 3
    },
    "INS_COSME_3": {
        "interseccion": "AV INSURGENTES CENTRO - AV. RIBERA DE SAN COSME",
        "direccion": "Av. Insurgentes Centro dir. Héroes Ferrocarrileros",
        "origen": (19.438291, -99.156267),
        "destino": (19.439603, -99.155704),
        "segment_id": 4
    },
    "INS_COSME_4": {
        "interseccion": "AV INSURGENTES CENTRO - AV. RIBERA DE SAN COSME",
        "direccion": "Av. Insurgentes Centro dir. Tomás Alva Edison",
        "origen": (19.441390, -99.155057),
        "destino": (19.440011, -99.155667),
        "segment_id": 5
    },
    "INS_EDISON_1": {
        "interseccion": "AV INSURGENTES CENTRO - TOMAS ALVA EDISON",
        "direccion": "Av. Insurgentes Centro dir. Av. Ribera San Cosme (Sur a Norte)",
        "origen": (19.436718, -99.156949),
        "destino": (19.438014, -99.156387),
        "segment_id": 6
    },
    "INS_EDISON_2": {
        "interseccion": "AV INSURGENTES CENTRO - TOMAS ALVA EDISON",
        "direccion": "Av. Insurgentes Centro dir. Reforma (Norte a Sur)",
        "origen": (19.439663, -99.155848),
        "destino": (19.438327, -99.156429),
        "segment_id": 7
    },
    "INS_EDISON_3": {
        "interseccion": "AV INSURGENTES CENTRO - TOMAS ALVA EDISON",
        "direccion": "Calle Tomás Alva Edison",
        "origen": (19.438497, -99.157297),
        "destino": (19.438216, -99.156597),
        "segment_id": 8
    },
    "ARRIAGA_EDISON_1": {
        "interseccion": "PONCIANO ARRIAGA Y TOMAS ALVA EDISON",
        "direccion": "Ponciano Arriaga dir. C. Ignacio Mariscal",
        "origen": (19.436746, -99.154064),
        "destino": (19.437284, -99.153872),
        "segment_id": 9
    },
    "ARRIAGA_EDISON_2": {
        "interseccion": "PONCIANO ARRIAGA Y TOMAS ALVA EDISON",
        "direccion": "Ponciano Arriaga dir. Plaza de la República",
        "origen": (19.438661, -99.153490),
        "destino": (19.437511, -99.153920),
        "segment_id": 10
    },
    "ARRIAGA_EDISON_3": {
        "interseccion": "PONCIANO ARRIAGA Y TOMAS ALVA EDISON",
        "direccion": "Tomás Alva Edison dir. Miguel Ramos",
        "origen": (19.437746, -99.155103),
        "destino": (19.437446, -99.154095),
        "segment_id": 11
    },
    "BUENAVISTA_TENOCH_1": {
        "interseccion": "BUENAVISTA Y AVENIDA MEXICO TENOCHTITLAN",
        "direccion": "Buenavista dir. Mina",
        "origen": (19.437763, -99.153700),
        "destino": (19.438595, -99.153368),
        "segment_id": 12
    },
    "BUENAVISTA_TENOCH_2": {
        "interseccion": "BUENAVISTA Y AVENIDA MEXICO TENOCHTITLAN",
        "direccion": "Buenavista dir. Tomás Alva Edison",
        "origen": (19.440221, -99.152876),
        "destino": (19.439128, -99.153335),
        "segment_id": 12
    },
    "BUENAVISTA_TENOCH_3": {
        "interseccion": "BUENAVISTA Y AVENIDA MEXICO TENOCHTITLAN",
        "direccion": "Av. México Tenochtitlan dir. Juan Aldama",
        "origen": (19.439540, -99.155214),
        "destino": (19.438842, -99.153576),
        "segment_id": 16
    },
    "BUENAVISTA_TENOCH_4": {
        "interseccion": "BUENAVISTA Y AVENIDA MEXICO TENOCHTITLAN",
        "direccion": "Av. México Tenochtitlan dir. José María Iglesias",
        "origen": (19.438335, -99.150695),
        "destino": (19.438854, -99.153098),
        "segment_id": 16
    },
    "JESUS_MINA_1": {
        "interseccion": "BUENAVISTA (JESUS GARCIA) Y MINA",
        "direccion": "Buenavista (Jesús García) dir. Violeta",
        "origen": (19.439052, -99.153188),
        "destino": (19.440697, -99.152546),
        "segment_id": 17
    },
    "JESUS_MINA_2": {
        "interseccion": "BUENAVISTA (JESUS GARCIA) Y MINA",
        "direccion": "Jesús García dir. Sur (Av. México Tenochtitlan)",
        "origen": (19.442214, -99.152544),
        "destino": (19.441017, -99.152642),
        "segment_id": 17
    },
    "JESUS_MINA_3": {
        "interseccion": "BUENAVISTA (JESUS GARCIA) Y MINA",
        "direccion": "Mina dir. Buenavista (Jesús García)",
        "origen": (19.440604, -99.151693),
        "destino": (19.440804, -99.152391),
        "segment_id": 18
    },
    "GUERRERO_HIDALGO_1": {
        "interseccion": "C. GUERRERO Y AV. HIDALGO",
        "direccion": "C. Guerrero (Norte a Sur)",
        "origen": (19.439691, -99.148802),
        "destino": (19.438150, -99.149071),
        "segment_id": 19
    },
    "GUERRERO_HIDALGO_2": {
        "interseccion": "C. GUERRERO Y AV. HIDALGO",
        "direccion": "Av. Hidalgo dir. Terán (Oriente a Poniente)",
        "origen": (19.437628, -99.147332),
        "destino": (19.437912, -99.148813),
        "segment_id": 20
    },
    "GUERRERO_HIDALGO_3": {
        "interseccion": "C. GUERRERO Y AV. HIDALGO",
        "direccion": "Av. Hidalgo dir. Sombrereros (Poniente a Oriente)",
        "origen": (19.438114, -99.150375),
        "destino": (19.437854, -99.149308),
        "segment_id": 20
    }
}

def calcular_distancia_y_sentido(lat1: float, lon1: float, lat2: float, lon2: float):
    R = 6371000.0
    phi1, phi2 = math.radians(lat1), math.radians(lat2)
    dphi = math.radians(lat2 - lat1)
    dlambda = math.radians(lon2 - lon1)
    a = math.sin(dphi / 2.0)**2 + math.cos(phi1) * math.cos(phi2) * math.sin(dlambda / 2.0)**2
    distancia_m = 2.0 * R * math.atan2(math.sqrt(a), math.sqrt(1.0 - a))

    y = math.sin(dlambda) * math.cos(phi2)
    x = math.cos(phi1) * math.sin(phi2) - math.sin(phi1) * math.cos(phi2) * math.cos(dlambda)
    brng = (math.degrees(math.atan2(y, x)) + 360.0) % 360.0

    if brng >= 315 or brng < 45:
        sentido = "Norte"
    elif 45 <= brng < 135:
        sentido = "Este (Poniente-Oriente)"
    elif 135 <= brng < 225:
        sentido = "Sur"
    else:
        sentido = "Oeste (Oriente-Poniente)"
    return round(distancia_m, 2), round(brng, 1), sentido

def buscar_aproximacion_mas_cercana(lat_orig: float, lon_orig: float, lat_dest: float, lon_dest: float):
    mejor_clave = "INS_COSME_1"
    menor_error = float("inf")
    for clave, info in CATALOGO_APROXIMACIONES.items():
        d_orig = math.hypot(lat_orig - info["origen"][0], lon_orig - info["origen"][1])
        d_dest = math.hypot(lat_dest - info["destino"][0], lon_dest - info["destino"][1])
        if (d_orig + d_dest) < menor_error:
            menor_error = d_orig + d_dest
            mejor_clave = clave
    return mejor_clave, CATALOGO_APROXIMACIONES[mejor_clave]