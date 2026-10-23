<?php
// Inicializar la sesión de PHP
session_start();

// Llamar al archivo de conexión (asegúrate de que conexion.php esté en la misma carpeta)
require_once 'conexion.php';

$error_login = "";

// Procesar el formulario de Login cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'login') {
    $usuario_ingresado = trim($_POST['usuario']);
    $password_ingresado = trim($_POST['password']);

    if (!empty($usuario_ingresado) && !empty($password_ingresado)) {
        try {
            $sql = "SELECT * FROM usuarios WHERE nombre_usuario = :usuario OR correo = :usuario LIMIT 1";
            $stmt = $conexion->prepare($sql);
            $stmt->execute(['usuario' => $usuario_ingresado]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (password_verify($password_ingresado, $user['password']) || $password_ingresado === $user['password']) {
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['nombre_usuario'] = $user['nombre_usuario'] ?? $user['correo'];
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $error_login = "Contraseña incorrecta.";
                }
            } else {
                $error_login = "El usuario o correo no existe.";
            }
        } catch (PDOException $e) {
            $error_login = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $error_login = "Por favor, completa todos los campos.";
    }
}

// Cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Boost | Eleva tu rendimiento escolar</title>
    
    <style>
        /* --- PALETA DE COLORES SUAVES (EYE-FRIENDLY & ANTI-DESLUMBRAMIENTO) --- */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, Roboto, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.25s ease, box-shadow 0.3s ease;
        }

        :root[data-theme="light"] {
            --bg-color: #eef2f6;
            --surface-color: #f8fafc;
            --surface-border: #cbd5e1;
            --text-color: #1e293b;
            --text-muted: #475569;
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --accent-purple: #9333ea;
            --accent-blue: #0284c7;

            --card-lavender: #f3f0ff;
            --border-lavender: #ddd6fe;
            --card-sky: #f0f9ff;
            --border-sky: #bae6fd;
            --card-mint: #f0fdf4;
            --border-mint: #bbf7d0;
            --card-amber: #fff7ed;
            --border-amber: #fed7aa;
            --shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.2);
        }

        :root[data-theme="dark"] {
            --bg-color: #0f172a;
            --surface-color: #1e293b;
            --surface-border: #334155;
            --text-color: #f1f5f9;
            --text-muted: #94a3b8;
            --primary-color: #818cf8;
            --primary-hover: #6366f1;
            --accent-purple: #c084fc;
            --accent-blue: #38bdf8;

            --card-lavender: #1e1b4b;
            --border-lavender: #3730a3;
            --card-sky: #0c4a6e;
            --border-sky: #0369a1;
            --card-mint: #064e3b;
            --border-mint: #047857;
            --card-amber: #451a03;
            --border-amber: #78350f;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* --- PANTALLA DE LOGIN OBLIGATORIO SI NO HA INICIADO SESIÓN --- */
        .login-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(6px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            padding: 1rem;
        }

        .login-card {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            padding: 2.5rem;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .login-card h2 {
            font-size: 1.8rem;
            text-align: center;
            color: var(--text-color);
            font-weight: 800;
        }

        .login-card h2 span {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-card p {
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-group input {
            padding: 0.75rem 1rem;
            border: 1px solid var(--surface-border);
            border-radius: 10px;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-size: 0.95rem;
            outline: none;
        }

        .form-group input:focus {
            border-color: var(--primary-color);
        }

        .btn-submit-login {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-purple));
            color: #ffffff;
            border: none;
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 0.5rem;
        }

        .btn-submit-login:hover {
            opacity: 0.9;
        }

        .error-msg {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 0.6rem;
            border-radius: 8px;
            font-size: 0.85rem;
            text-align: center;
            font-weight: 600;
        }

        /* --- TOAST NOTIFICATIONS --- */
        #toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            background-color: var(--surface-color);
            color: var(--text-color);
            border-left: 4px solid var(--primary-color);
            padding: 0.8rem 1.2rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            font-size: 0.9rem;
            font-weight: 600;
            animation: slideIn 0.3s ease forwards;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* --- NAVBAR --- */
        .navbar-header {
            background-color: var(--surface-color);
            border-bottom: 1px solid var(--surface-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.9rem 5%;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-color);
            letter-spacing: -0.5px;
        }

        .logo span {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-controls {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .navbar {
            display: flex;
            gap: 0.3rem;
        }

        .nav-btn {
            text-decoration: none;
            color: var(--text-muted);
            padding: 0.5rem 0.9rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .nav-btn:hover {
            color: var(--primary-color);
            background-color: var(--bg-color);
        }

        .nav-btn.active {
            background-color: var(--primary-color);
            color: #ffffff;
        }

        .action-btn {
            background: var(--bg-color);
            border: 1px solid var(--surface-border);
            padding: 0.5rem 0.85rem;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.88rem;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 600;
            text-decoration: none;
        }

        .action-btn:hover {
            border-color: var(--primary-color);
            transform: translateY(-1px);
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .menu-toggle span {
            width: 24px;
            height: 3px;
            background-color: var(--text-color);
            border-radius: 3px;
        }

        /* --- CONTENIDO PRINCIPAL --- */
        main { flex: 1; }

        .top-hero-carousel {
            position: relative;
            width: 100%;
            height: 360px;
            overflow: hidden;
            background-color: #0b1329;
        }

        .hero-slide-img {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover; opacity: 0.38;
            transition: opacity 0.8s ease-in-out, transform 8s ease;
        }

        .hero-overlay-content {
            position: relative; z-index: 2; max-width: 950px; height: 100%;
            margin: 0 auto; padding: 0 2rem; display: flex; flex-direction: column;
            justify-content: center; align-items: center; text-align: center; color: #ffffff;
        }

        .hero-title-badge {
            display: inline-block; background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px); padding: 0.35rem 1.1rem; border-radius: 30px;
            font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.5px; margin-bottom: 0.9rem; border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .hero-overlay-content h2 { font-size: 2.6rem; font-weight: 800; line-height: 1.2; margin-bottom: 0.8rem; text-shadow: 0 2px 8px rgba(0,0,0,0.4); }
        .hero-overlay-content p { font-size: 1.1rem; max-width: 680px; color: #e2e8f0; margin-bottom: 1.2rem; text-shadow: 0 1px 4px rgba(0,0,0,0.4); }

        .carousel-dots {
            position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%);
            display: flex; gap: 8px; z-index: 3;
        }
        .dot { width: 9px; height: 9px; border-radius: 50%; background: rgba(255,255,255,0.4); cursor: pointer; }
        .dot.active { background: #ffffff; width: 22px; border-radius: 10px; }

        .welcome-container { max-width: 1050px; margin: 2.5rem auto 1.5rem auto; padding: 0 1.5rem; }
        .welcome-card {
            background: linear-gradient(135deg, var(--card-lavender), var(--card-sky));
            border: 1px solid var(--border-lavender); border-radius: 20px;
            padding: 2.2rem; box-shadow: var(--shadow); display: flex; flex-direction: column; gap: 1.5rem;
        }
        .welcome-header { display: flex; align-items: center; gap: 0.8rem; border-bottom: 1px dashed var(--border-lavender); padding-bottom: 1rem; }
        .welcome-header h3 { font-size: 1.4rem; color: var(--text-color); font-weight: 800; }
        .welcome-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        .welcome-block { display: flex; flex-direction: column; gap: 0.6rem; }
        .welcome-block h4 { font-size: 1.1rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem; }
        .welcome-block p { font-size: 0.96rem; color: var(--text-muted); line-height: 1.65; }

        .workspace-section {
            max-width: 1050px; margin: 1.5rem auto 2.5rem auto; padding: 0 1.5rem;
            display: grid; grid-template-columns: 1fr 1fr; gap: 1.8rem;
        }
        .interactive-tip-box {
            background-color: var(--card-mint); border: 1px solid var(--border-mint);
            padding: 1.8rem; border-radius: 20px; box-shadow: var(--shadow);
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .tip-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .tip-tag { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #15803d; background-color: rgba(34, 197, 94, 0.15); padding: 0.3rem 0.8rem; border-radius: 20px; }
        .btn-tip { background-color: #16a34a; color: #ffffff; border: none; padding: 0.4rem 0.9rem; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; }
        .tip-content { font-size: 1.02rem; font-weight: 600; color: var(--text-color); min-height: 3.2em; display: flex; align-items: center; }

        .task-card { background-color: var(--card-amber); border: 1px solid var(--border-amber); padding: 1.8rem; border-radius: 20px; box-shadow: var(--shadow); }
        .task-card h3 { font-size: 1.15rem; margin-bottom: 1rem; color: var(--text-color); display: flex; align-items: center; gap: 0.5rem; }
        .task-input-group { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
        .task-input-group input { flex: 1; padding: 0.65rem 0.9rem; border: 1px solid var(--border-amber); border-radius: 10px; background-color: var(--surface-color); color: var(--text-color); outline: none; font-size: 0.92rem; }
        .btn-add-task { background-color: #ea580c; color: #ffffff; border: none; padding: 0.65rem 1.1rem; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 0.9rem; }
        .task-list { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; max-height: 140px; overflow-y: auto; }
        .task-item { display: flex; justify-content: space-between; align-items: center; padding: 0.55rem 0.8rem; background-color: var(--surface-color); border-radius: 8px; font-size: 0.88rem; border: 1px solid rgba(0,0,0,0.04); }
        .task-item.completed span { text-decoration: line-through; color: var(--text-muted); }
        .task-actions { display: flex; gap: 0.4rem; }
        .btn-check, .btn-del { border: none; background: none; cursor: pointer; font-size: 0.9rem; }

        .resources-section { max-width: 1050px; margin: 1.5rem auto 3.5rem auto; padding: 0 1.5rem; }
        .section-title { text-align: center; font-size: 1.5rem; margin-bottom: 1.8rem; color: var(--text-color); font-weight: 800; }
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.8rem; }
        .feature-card-btn { background-color: var(--surface-color); border: 1px solid var(--surface-border); border-radius: 20px; overflow: hidden; cursor: pointer; text-align: left; display: flex; flex-direction: column; box-shadow: var(--shadow); text-decoration: none; position: relative; }
        .feature-card-btn:hover { transform: translateY(-6px); border-color: var(--primary-color); box-shadow: 0 15px 30px -10px rgba(99, 102, 241, 0.25); }
        .card-img-wrapper { position: relative; height: 175px; width: 100%; overflow: hidden; }
        .card-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
        .feature-card-btn:hover .card-img-wrapper img { transform: scale(1.08); }
        .card-body { padding: 1.4rem; display: flex; flex-direction: column; gap: 0.5rem; flex: 1; }
        .card-body h4 { font-size: 1.15rem; color: var(--text-color); font-weight: 700; }
        .card-body p { font-size: 0.88rem; color: var(--text-muted); line-height: 1.55; }
        .card-action-text { margin-top: auto; font-size: 0.85rem; font-weight: 700; color: var(--primary-color); display: flex; align-items: center; gap: 0.3rem; padding-top: 0.8rem; }

        .footer { background-color: var(--surface-color); border-top: 1px solid var(--surface-border); padding: 2rem 1rem 1.5rem 1rem; text-align: center; margin-top: auto; }
        .footer-quote-container { max-width: 600px; margin: 0 auto 1.5rem auto; padding: 1.2rem; background-color: var(--card-lavender); border-left: 4px solid var(--primary-color); border-radius: 14px; }
        .footer-quote-container h3 { font-size: 0.78rem; color: var(--primary-color); margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        #daily-quote { font-size: 0.98rem; color: var(--text-color); font-style: italic; margin-bottom: 0.8rem; }
        .btn-refresh-quote { background: var(--surface-color); border: 1px solid var(--surface-border); color: var(--text-muted); padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; cursor: pointer; font-weight: 600; }
        .footer-bottom { font-size: 0.85rem; color: var(--text-muted); }

        @media (max-width: 900px) {
            .welcome-grid, .workspace-section { grid-template-columns: 1fr; }
            .top-hero-carousel { height: 310px; }
            .hero-overlay-content h2 { font-size: 2rem; }
        }
        @media (max-width: 768px) {
            .menu-toggle { display: flex; }
            .navbar { display: none; position: absolute; top: 100%; left: 0; width: 100%; background-color: var(--surface-color); flex-direction: column; padding: 1rem; border-bottom: 1px solid var(--surface-border); }
            .navbar.open { display: flex; }
        }
    </style>
</head>
<body>

    <!-- SI NO HA INICIADO SESIÓN, SE MUESTRA EL LOGIN -->
    <?php if (!isset($_SESSION['usuario_id'])): ?>
    <div class="login-overlay">
        <div class="login-card">
            <h2>Study<span>Boost</span></h2>
            <p>Inicia sesión con tu cuenta para acceder a tu panel de estudio.</p>
            
            <?php if (!empty($error_login)): ?>
                <div class="error-msg"><?php echo $error_login; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="hidden" name="accion" value="login">
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="usuario">Usuario o Correo</label>
                    <input type="text" id="usuario" name="usuario" required placeholder="Ej: admin o tu correo">
                </div>

                <div class="form-group" style="margin-bottom: 1.2rem;">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn-submit-login">Ingresar al Sistema</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="focus-banner" id="focus-banner">
    </div>

    <div id="toast-container"></div>

    <header class="navbar-header hide-on-focus">
        <div class="logo">
            <h1>Study<span>Boost</span> 
                <?php if (isset($_SESSION['nombre_usuario'])): ?>
                    <small style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;">(Hola, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>)</small>
                <?php endif; ?>
            </h1>
        </div>

        <div class="nav-controls">
            <button class="action-btn" id="focus-btn" title="Modo No Molestar">
                <span>🔕</span> <small>No Molestar</small>
            </button>

            <button class="action-btn" id="theme-btn" title="Cambiar tema visual">
                <span id="theme-icon">🌙</span>
            </button>

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="?logout=true" class="action-btn" style="color: #ef4444; border-color: #fca5a5;" title="Cerrar Sesión">
                    <span>🚪</span> <small>Salir</small>
                </a>
            <?php endif; ?>

            <button class="menu-toggle" id="menu-toggle" aria-label="Abrir Menú">
                <span></span><span></span><span></span>
            </button>
        </div>

        <nav class="navbar" id="navbar">
            <a href="introduccion.php" class="nav-btn active">Inicio</a>
            <a href="tecnicas.html" class="nav-btn">Técnicas</a>
            <a href="calendario.php" class="nav-btn">Calendario</a>
            <a href="videos.html" class="nav-btn">Videos</a>
            <a href="recordatorio.php" class="nav-btn">Recordatorio</a>
            <a href="metas.php" class="nav-btn">Metas</a>
        </nav>
    </header>

    <main>
        <section class="top-hero-carousel hide-on-focus">
            <!-- SE AÑADIÓ UN SRC INICIAL PARA EVITAR EL ERROR DE IMAGEN ROTA -->
            <img id="hero-bg-img" class="hero-slide-img" src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1200&q=80" alt="Fondo de Estudio">
            
            <div class="hero-overlay-content">
                <span class="hero-title-badge">Proyecto BTH Sistemas Informáticos</span>
                <h2 id="hero-title">StudyBoost</h2>
                <p id="hero-desc">Tu espacio para optimizar el tiempo, vencer la procrastinación y estudiar de manera inteligente.</p>
            </div>

            <div class="carousel-dots" id="hero-dots"></div>
        </section>

        <section class="welcome-container hide-on-focus">
            <div class="welcome-card">
                <div class="welcome-header">
                    <span>✨</span>
                    <h3>Bienvenido a Study Boost</h3>
                </div>
                <div class="welcome-grid">
                    <div class="welcome-block">
                        <h4>👋 Presentación</h4>
                        <p>
                            ¡Hola! Te damos la bienvenida a <strong>Study Boost</strong>. Esta plataforma ha sido desarrollada por estudiantes del BTH pensada especialmente para acompañarte en tu día a día académico. Sabemos lo agotador que puede ser acumular tareas o no saber por dónde empezar; por eso creamos este espacio dinámico, cómodo y práctico.
                        </p>
                    </div>
                    <div class="welcome-block">
                        <h4>🚀 Introducción</h4>
                        <p>
                            El secreto del éxito escolar no es estudiar más horas sufriendo frente a los libros, sino <strong>estudiar de forma inteligente</strong>. Aquí aprenderás técnicas comprobadas como Pomodoro, Active Recall y mapas de prioridades, diseñadas para mejorar tu concentración, reducir el estrés y asegurarte de tener tiempo libre para disfrutar.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="workspace-section">
            <div class="interactive-tip-box">
                <div>
                    <div class="tip-header">
                        <span class="tip-tag">💡 Tip de Enfoque Rápido</span>
                        <button class="btn-tip" id="next-tip-btn">Siguiente 🎲</button>
                    </div>
                    <p class="tip-content" id="tip-display">Cargando recomendación...</p>
                </div>
            </div>

            <div class="task-card">
                <h3>📌 Mis Pendientes de Hoy</h3>
                <div class="task-input-group">
                    <input type="text" id="task-input" placeholder="Ej: Repasar Tema 2 de Sistemas...">
                    <button class="btn-add-task" id="add-task-btn">Agregar</button>
                </div>
                <ul class="task-list" id="task-list"></ul>
            </div>
        </section>

        <section class="resources-section hide-on-focus">
            <h3 class="section-title">Herramientas de Diagnóstico y Orientación</h3>
            
            <div class="cards-grid">
                <div class="feature-card-btn" id="btn-mitos">
                    <!-- SE AÑADIÓ UN SRC INICIAL VÁLIDO -->
                    <div class="card-img-wrapper"><img id="img-mito" src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80" alt="mitos"></div>
                    <div class="card-body">
                        <h4>Mitos sobre el Estudio</h4>
                        <p>Descubre qué creencias comunes te están ralentizando y cuál es la verdad detrás del aprendizaje activo.</p>
                        <span class="card-action-text">Explorar Mitos ➔</span>
                    </div>
                </div>

                <div class="feature-card-btn" id="btn-diagnostico">
                    <!-- SE AÑADIÓ UN SRC INICIAL VÁLIDO -->
                    <div class="card-img-wrapper"><img id="img-diag" src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80" alt="Diagnóstico"></div>
                    <div class="card-body">
                        <h4>Diagnóstico para Estudiantes</h4>
                        <p>Responde una autoevaluación rápida para descubrir cuánto apoyo necesitas en la gestión de tus horarios.</p>
                        <span class="card-action-text">Iniciar Diagnóstico ➔</span>
                    </div>
                </div>

                <div class="feature-card-btn" id="btn-faq">
                    <!-- SE AÑADIÓ UN SRC INICIAL VÁLIDO -->
                    <div class="card-img-wrapper"><img id="img-faq" src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=600&q=80" alt="FAQ"></div>
                    <div class="card-body">
                        <h4>Preguntas Frecuentes</h4>
                        <p>Respuestas claras a las dudas más importantes sobre técnicas de organización de tiempo.</p>
                        <span class="card-action-text">Ver Preguntas ➔</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer hide-on-focus">
        <div class="footer-quote-container">
            <h3>💡 Frase Motivadora del Día</h3>
            <blockquote id="daily-quote">Cargando frase...</blockquote>
            <button class="btn-refresh-quote" id="refresh-quote-btn">Cambiar Frase 🔄</button>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Study Boost - Proyecto de Innovación BTH Sistemas Informáticos.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function showToast(message) {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = 'toast';
                toast.textContent = message;
                container.appendChild(toast);
                setTimeout(() => { toast.remove(); }, 3000);
            }

            const themeBtn = document.getElementById('theme-btn');
            const themeIcon = document.getElementById('theme-icon');
            const root = document.documentElement;
            const savedTheme = localStorage.getItem('sb-theme') || 'light';
            setTheme(savedTheme, false);

            themeBtn.addEventListener('click', () => {
                const currentTheme = root.getAttribute('data-theme');
                const nextTheme = currentTheme === 'light' ? 'dark' : 'light';
                setTheme(nextTheme, true);
            });

            function setTheme(theme, notify = false) {
                root.setAttribute('data-theme', theme);
                localStorage.setItem('sb-theme', theme);
                themeIcon.textContent = theme === 'light' ? '🌙' : '☀️';
                if(notify) showToast(`Modo ${theme === 'light' ? 'Claro Suave' : 'Oscuro Soft'} activado`);
            }

            const focusBtn = document.getElementById('focus-btn');
            focusBtn.addEventListener('click', () => { document.body.classList.add('focus-mode'); showToast('Modo No Molestar Activado 🔕'); });

            const menuToggle = document.getElementById('menu-toggle');
            const navbar = document.getElementById('navbar');
            menuToggle.addEventListener('click', () => navbar.classList.toggle('open'));

            const heroSlides = [
                { url: 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1200&q=80', title: 'Organiza tu Tiempo', desc: 'Aprende a estructurar tus jornadas de estudio sin estrés.' },
                { url: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80', title: 'Enfoque y Concentración', desc: 'Elimina distracciones y aprovecha al máximo cada minuto.' },
                { url: 'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?auto=format&fit=crop&w=1200&q=80', title: 'Alcanza tus Metas', desc: 'Construye hábitos sostenibles.' }
            ];

            let heroIdx = 0;
            const heroImg = document.getElementById('hero-bg-img');
            const heroTitle = document.getElementById('hero-title');
            const heroDesc = document.getElementById('hero-desc');
            const heroDotsContainer = document.getElementById('hero-dots');

            heroSlides.forEach((_, i) => {
                const dot = document.createElement('div');
                dot.classList.add('dot');
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => setHeroSlide(i));
                heroDotsContainer.appendChild(dot);
            });
            const dots = heroDotsContainer.querySelectorAll('.dot');

            function setHeroSlide(index) {
                heroIdx = index;
                heroImg.style.opacity = '0.1';
                setTimeout(() => {
                    heroImg.src = heroSlides[heroIdx].url;
                    heroTitle.textContent = heroSlides[heroIdx].title;
                    heroDesc.textContent = heroSlides[heroIdx].desc;
                    heroImg.style.opacity = '0.38';
                }, 300);
                dots.forEach((d, i) => d.classList.toggle('active', i === heroIdx));
            }
            setHeroSlide(0);
            setInterval(() => { heroIdx = (heroIdx + 1) % heroSlides.length; setHeroSlide(heroIdx); }, 5000);

            const mitosImages = ['https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80', 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=600&q=80'];
            const diagImages = ['https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80', 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?auto=format&fit=crop&w=600&q=80'];
            const faqImages = ['https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=600&q=80', 'https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=600&q=80'];

            let cardImgIdx = 0;
            const imgMitoEl = document.getElementById('img-mito');
            const imgDiagEl = document.getElementById('img-diag');
            const imgFaqEl = document.getElementById('img-faq');

            function updateCardImages() {
                imgMitoEl.src = mitosImages[cardImgIdx % mitosImages.length];
                imgDiagEl.src = diagImages[cardImgIdx % diagImages.length];
                imgFaqEl.src = faqImages[cardImgIdx % faqImages.length];
                cardImgIdx++;
            }
            setInterval(updateCardImages, 5000);

            document.getElementById('btn-mitos').addEventListener('click', () => { window.location.href = 'mitos.html'; });
            document.getElementById('btn-diagnostico').addEventListener('click', () => { window.location.href = 'diagnostico.html'; });
            document.getElementById('btn-faq').addEventListener('click', () => { window.location.href = 'faq.html'; });

            const studyTips = [
                "Aplica el principio 80/20: Enfócate en el 20% del contenido clave.",
                "Usa la técnica Feynman: Explica un tema complejo con palabras sencillas.",
                "Deja tu teléfono en otra habitación durante tus bloques de concentración.",
                "Active Recall: Ponte a prueba sin mirar tus apuntes para fijar el conocimiento.",
                "Organiza tus repasos usando mapas conceptuales dinámicos."
            ];

            let tipIdx = 0;
            const tipDisplay = document.getElementById('tip-display');
            const nextTipBtn = document.getElementById('next-tip-btn');

            function showTip(idx) {
                tipDisplay.style.opacity = '0';
                setTimeout(() => {
                    tipDisplay.textContent = studyTips[idx];
                    tipDisplay.style.opacity = '1';
                }, 150);
            }
            nextTipBtn.addEventListener('click', () => { tipIdx = (tipIdx + 1) % studyTips.length; showTip(tipIdx); });
            showTip(0);

            const taskInput = document.getElementById('task-input');
            const addTaskBtn = document.getElementById('add-task-btn');
            const taskList = document.getElementById('task-list');

            let tasks = JSON.parse(localStorage.getItem('sb-tasks')) || [{ text: 'Revisar mi plan de estudio semanal', completed: false }];

            function renderTasks() {
                taskList.innerHTML = '';
                tasks.forEach((t, index) => {
                    const li = document.createElement('li');
                    li.className = `task-item ${t.completed ? 'completed' : ''}`;
                    li.innerHTML = `
                        <span>${t.text}</span>
                        <div class="task-actions">
                            <button class="btn-check" onclick="toggleTask(${index})">✔</button>
                            <button class="btn-del" onclick="deleteTask(${index})">🗑</button>
                        </div>
                    `;
                    taskList.appendChild(li);
                });
                localStorage.setItem('sb-tasks', JSON.stringify(tasks));
            }

            addTaskBtn.addEventListener('click', () => {
                const val = taskInput.value.trim();
                if (val) {
                    tasks.push({ text: val, completed: false });
                    taskInput.value = '';
                    renderTasks();
                    showToast('Pendiente guardado 📝');
                }
            });

            window.toggleTask = function(idx) { tasks[idx].completed = !tasks[idx].completed; renderTasks(); };
            window.deleteTask = function(idx) { tasks.splice(idx, 1); renderTasks(); showToast('Pendiente eliminado'); };
            renderTasks();

            const quotes = [
                '"El éxito es la suma de pequeños esfuerzos repetidos día tras día." — Robert Collier',
                '"La disciplina es el puente entre tus metas y tus logros." — Jim Rohn',
                '"El secreto para salir adelante es simplemente empezar." — Mark Twain',
                '"Haz de cada día tu obra maestra." — John Wooden'
            ];
            const dailyQuoteEl = document.getElementById('daily-quote');
            const refreshQuoteBtn = document.getElementById('refresh-quote-btn');
            function setRandomQuote() { dailyQuoteEl.textContent = quotes[Math.floor(Math.random() * quotes.length)]; }
            refreshQuoteBtn.addEventListener('click', setRandomQuote);
            setRandomQuote();
        });
    </script>
</body>
</html>