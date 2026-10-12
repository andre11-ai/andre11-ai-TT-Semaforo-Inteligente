<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Mando - Sistema de Tráfico</title>
    <link rel="stylesheet" href="{{ asset('./CSS/Centro-mando.css') }}">
</head>
<body>

<nav class="header-dark">
        <div class="header-title">
            <span>🚦 Sistema de Tráfico</span>
            <span class="header-separator">|</span>
            <span class="header-subtitle">Monitor Global de Intersecciones</span>
        </div>
        
        @php
            $esAdmin = strtolower(auth()->user()->rol ?? auth()->user()->role) === 'administrador' || strtolower(auth()->user()->rol ?? auth()->user()->role) === 'admin';
        @endphp

        @if($esAdmin)
            <a href="{{ route('admin.dashboard') }}" class="btn-volver">⬅ Volver</a>
        @else
            <a href="{{ route('usuario.dashboard') }}" class="btn-volver">⬅ Volver</a>
        @endif
    </nav>

    <main class="main-container">

        <div class="header-flex">
            <div>
                <h1>Red de Semáforos</h1>
                <p>Selecciona una intersección para extraer telemetría y datos de la Inteligencia Artificial en tiempo real.</p>
            </div>
        </div>

        <div class="grid-monitores">
            @forelse ($intersecciones as $interseccion)
                <div class="monitor-card {{ strtolower($interseccion->estado_operativo) }}">
                    <div class="card-header">
                        <h2 class="card-title">{{ $interseccion->nombre }}</h2>
                        <span class="badge {{ strtolower($interseccion->estado_operativo) }}">{{ $interseccion->estado_operativo }}</span>
                    </div>

                    <div class="info-row">
                        <strong>Dirección IP del Controlador</strong>
                        {{ $interseccion->ip_controlador }}
                    </div>

                    <div class="info-row" style="margin-top: 15px;">
                        <strong>Coordenadas GPS</strong>
                        <span style="font-family: monospace;">{{ $interseccion->latitud }}, {{ $interseccion->longitud }}</span>
                    </div>

                    <a href="{{ route('monitor.detalle', $interseccion->id) }}" class="btn-ver">👁️ Ver Detalles en Vivo</a>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #a0aec0; background: white; border-radius: 8px;">
                    <span style="font-size: 3rem;">📭</span><br>
                    No hay intersecciones registradas en la red.
                </div>
            @endforelse
        </div>

    </main>

</body>
</html>
