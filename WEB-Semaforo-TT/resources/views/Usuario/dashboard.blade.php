<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Operativo - Sistema de Tráfico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8fafc; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-dark { background-color: #1a202c; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header-title { font-size: 1.2rem; font-weight: bold; display: flex; align-items: center; gap: 10px; }

        .user-info { display: flex; align-items: center; gap: 15px; font-size: 0.95rem; }
        .btn-logout { background-color: transparent; color: #fc8181; border: 1px solid #e53e3e; padding: 6px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; transition: 0.3s; font-weight: bold; cursor: pointer; }
        .btn-logout:hover { background-color: #e53e3e; color: white; }

        .welcome-section { text-align: center; margin: 60px 20px 40px; }
        .welcome-title { font-size: 2.2rem; color: #2d3748; margin: 0 0 10px 0; font-weight: 800; }
        .welcome-subtitle { font-size: 1.1rem; color: #718096; margin: 0; }

        .modules-container { display: flex; justify-content: center; align-items: center; padding: 0 20px 50px; }

        .module-card { background: white; border-radius: 12px; padding: 50px 30px; text-align: center; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02); text-decoration: none; color: inherit; transition: all 0.3s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; max-width: 400px; }
        .module-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0,0,0,0.08); border-color: #3b5998; }

        .module-icon { font-size: 3.5rem; color: #3b5998; margin-bottom: 20px; }
        .module-title { font-size: 1.4rem; font-weight: bold; color: #1a202c; margin-bottom: 12px; }
        .module-desc { font-size: 0.95rem; color: #a0aec0; line-height: 1.5; }
    </style>
</head>
<body>

    <nav class="header-dark">
        <div class="header-title">🚦 Sistema de Tráfico</div>
        <div class="user-info">
            <span>¡Bienvenido, <strong>{{ auth()->user()->name ?? 'Operador' }}</strong>!</span>

            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">Cerrar Sesión</button>
            </form>
        </div>
    </nav>

    <section class="welcome-section">
        <h1 class="welcome-title">Panel de Operador</h1>
        <p class="welcome-subtitle">Acceso autorizado a la red de monitoreo y telemetría de semáforos en tiempo real.</p>
    </section>

    <main class="modules-container">

        <a href="{{ route('monitor.global') }}" class="module-card">
            <i class="fa-solid fa-display module-icon"></i>
            <div class="module-title">Monitor en Vivo</div>
            <div class="module-desc">Visualiza el estatus de las intersecciones, cámaras y flujo vehicular de la ciudad.</div>
        </a>

    </main>

</body>
</html>
