<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Dashboard Admin - Sistema de Control Vial</title>
    <link rel="stylesheet" href="{{ asset('./CSS/Admin.css') }}">
</head>
<body>


    <nav class="navbar">
        <div class="navbar-brand">
            <span class="logo-icon">🚦</span>
            <span class="logo-text">Sistema de Tráfico</span>
        </div>
        <div class="navbar-user">
            <span class="welcome-text">¡Bienvenido, <strong>{{ auth()->user()->name }}</strong>!</span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Cerrar Sesión</button>
            </form>
        </div>
    </nav>

    <main class="dashboard-container">
        <div class="dashboard-header">
            <h1>¿Qué deseas hacer hoy?</h1>
            <p>Selecciona un módulo para gestionar la plataforma</p>
        </div>

        <div class="modules-grid">

            <a href="{{ route('prototipos.create') }}" class="module-card">
                <div class="card-icon">➕</div>
                <h3>Crear Prototipo</h3>
                <p>Añadir una nueva intersección de tráfico a la red inteligente.</p>
            </a>

            <a href="{{ route('prototipos.index') }}" class="module-card">
                <div class="card-icon">🎛️</div>
                <h3>Mis Prototipos</h3>
                <p>Gestionar, editar y monitorear los semáforos activos en tiempo real.</p>
            </a>

            <a href="{{ route('monitor.global') }}" class="module-card">
                <div class="card-icon">👁️</div>
                <h2 class="card-title">Monitor en Vivo</h2>
                <p class="card-desc">Visualizar el estatus de las intersecciones y las decisiones de la IA en tiempo real.</p>
            </a>

            <a href="{{ route('usuarios.index') }}" class="module-card">
                <div class="card-icon">👥</div>
                <h3>Gestión de Usuarios</h3>
                <p>Administrar cuentas, perfiles y accesos operativos del sistema.</p>
            </a>

            <a href="{{ route('diccionario.index') }}" class="module-card">
                <div class="card-icon">🧠</div>
                <h3>Diccionario PLN</h3>
                <p>Configurar palabras clave y pesos de severidad para la IA.</p>
            </a>

            <a href="{{ route('historial.index') }}" class="module-card">
                <div class="card-icon">📋</div>
                <h3>Historial de Operaciones</h3>
                <p>Auditar activaciones del Modo Forzado y eventos del sistema.</p>
            </a>

        </div>
    </main>
</body>
</html>
