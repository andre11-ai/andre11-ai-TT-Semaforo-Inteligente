<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Sistema de Control Vial</title>
    <link rel="stylesheet" href="{{ asset('./CSS/Login.css') }}">
</head>
<body>
    <canvas id="network-canvas"></canvas>

    <div class="container" id="container">
        <div class="visual-container">
            <div class="traffic-light-body">
                <div class="light-wrapper">
                    <div class="hood"></div>
                    <div class="light red" id="light-red"></div>
                </div>
                <div class="light-wrapper">
                    <div class="hood"></div>
                    <div class="light yellow" id="light-yellow"></div>
                </div>
                <div class="light-wrapper">
                    <div class="hood"></div>
                    <div class="light green" id="light-green"></div>
                </div>
            </div>
        </div>

        <div class="form-container register-container">
            <form id="form-register" action="/register" method="POST">
                @csrf
                <input type="hidden" name="rol" value="Usuario">
                <h1 class="auth-title">Crear Cuenta</h1>
                <p class="auth-subtitle">Regístrate como operador vial</p>

                <div class="input-group">
                    <label>Nombre Completo</label>
                    <input type="text" id="reg-name" name="name" required>
                </div>
                <div class="input-group">
                    <label>Correo Institucional</label>
                    <input type="email" id="reg-email" name="email" required>
                    <span class="error-msg" id="error-reg-email"></span>
                </div>
                <div class="input-group">
                    <label>Contraseña</label>
                    <div class="password-wrapper">
                        <input type="password" id="reg-password" name="password" placeholder="Mín. 8 chars, 1 mayús, 1 núm" required>
                        <span class="toggle-password" onclick="togglePassword('reg-password')">👁️</span>
                    </div>
                    <span class="error-msg" id="error-reg-password"></span>
                </div>
                <div class="input-group">
                    <label>Confirmar Contraseña</label>
                    <div class="password-wrapper">
                        <input type="password" id="reg-password-confirm" name="password_confirmation" required>
                        <span class="toggle-password" onclick="togglePassword('reg-password-confirm')">👁️</span>
                    </div>
                    <span class="error-msg" id="error-reg-confirm"></span>
                </div>
                <button type="submit" class="btn-primary">Registrarse</button>

                <div class="auth-links">
                    <span class="link-text" id="to-login">Ya tengo cuenta, Iniciar sesión</span>
                </div>
            </form>
        </div>

        <div class="form-container login-container">
            <form id="form-login" action="/login" method="POST">
                @csrf
                <h1 class="auth-title">Bienvenido</h1>
                <p class="auth-subtitle">Ingresa tus credenciales para continuar</p>

                @if($errors->any())
                    <span class="error-msg" style="margin-bottom:15px; display:block;">{{ $errors->first() }}</span>
                @endif

                <div class="input-group">
                    <label>Correo Institucional</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="input-group">
                    <label>Contraseña</label>
                    <div class="password-wrapper">
                        <input type="password" id="login-password" name="password" placeholder="********" required>
                        <span class="toggle-password" onclick="togglePassword('login-password')">👁️</span>
                    </div>
                </div>
                <button type="submit" class="btn-primary">Login</button>

                <div class="auth-links">
                    <a href="#" class="forgot-pass">¿Olvidaste tu contraseña?</a>
                    <span class="link-text" id="to-register"></span>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('./JS/Login.js') }}"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        async function cicloDeUnaHora() {
            try {
                let res = await fetch('/api/semaforos-inteligentes');
                let data = await res.json();

                if (!data.ejecutar) {
                    console.log("⏱️ [Sync 1h] " + data.mensaje);
                } else {
                    console.group('%c🚦 Iniciando Sincronización Global (Ciclo 1 Hora)', 'color: #38a169; font-weight: bold;');
                    for (let int of data.semaforos) {
                        console.log(`📡 Consultando: ${int.nombre}...`);
                        try {
                            await fetch(`/api/monitor/datos/${int.id}`);
                            console.log(`✅ Guardado: ${int.nombre}`);
                        } catch (e) {
                            console.error(`❌ Error en ${int.nombre}`);
                        }
                        await new Promise(r => setTimeout(r, 3000));
                    }
                    console.groupEnd();
                }
            } catch (e) {
                console.error("Error de sincronización:", e);
            }
            setTimeout(cicloDeUnaHora, 3600000);
        }

        setTimeout(cicloDeUnaHora, 2000);
    });
</script>
</body>
</html>
