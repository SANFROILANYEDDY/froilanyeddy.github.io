<?php
// Esto llama al archivo de conexión con la base de datos
require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Boost | Calendario de Estudio</title>

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

        body.focus-mode .hide-on-focus {
            display: none !important;
        }

        .focus-banner {
            display: none;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-purple));
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            position: sticky;
            top: 0;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        body.focus-mode .focus-banner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 5%;
        }

        .btn-exit-focus {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #ffffff;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .btn-exit-focus:hover {
            background: rgba(255, 255, 255, 0.35);
        }

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

        main {
            flex: 1;
        }

        /* --- BARRA DE VOLVER --- */
        .back-bar {
            max-width: 1250px;
            margin: 1.4rem auto 0 auto;
            padding: 0 1.5rem;
        }

        .btn-back {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
        }

        /* --- HERO COMPACTO CON CARRUSEL --- */
        .top-hero-carousel {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
            background-color: #0b1329;
            margin-top: 1rem;
            border-radius: 0;
        }

        .hero-slide-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.38;
            transition: opacity 0.8s ease-in-out, transform 8s ease;
        }

        .top-hero-carousel:hover .hero-slide-img {
            transform: scale(1.03);
        }

        .hero-overlay-content {
            position: relative;
            z-index: 2;
            max-width: 950px;
            height: 100%;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #ffffff;
        }

        .hero-title-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 0.35rem 1.1rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .hero-overlay-content h2 {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 0.8rem;
            text-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }

        .hero-overlay-content p {
            font-size: 1.05rem;
            max-width: 680px;
            color: #e2e8f0;
            margin-bottom: 1.2rem;
            text-shadow: 0 1px 4px rgba(0,0,0,0.4);
        }

        .carousel-dots {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 3;
        }

        .dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: rgba(255,255,255,0.4);
            cursor: pointer;
        }

        .dot.active {
            background: #ffffff;
            width: 22px;
            border-radius: 10px;
        }

        /* --- HORARIO SEMANAL DE 7 DÍAS CON FRANJAS DE 40 MINUTOS --- */
        .weekly-schedule-container {
            max-width: 1250px;
            margin: 2.2rem auto;
            padding: 0 1.5rem;
        }

        .schedule-card {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 20px;
            padding: 1.8rem;
            box-shadow: var(--shadow);
            overflow-x: auto;
            margin-bottom: 2.2rem;
        }

        .schedule-header-title {
            font-size: 1.35rem;
            font-weight: 800;
            margin-bottom: 0.4rem;
        }

        .schedule-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1.4rem;
        }

        .schedule-grid-table {
            display: grid;
            grid-template-columns: 110px repeat(7, 1fr);
            gap: 8px;
            min-width: 950px;
        }

        .schedule-col-header {
            text-align: center;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            padding: 0.6rem 0.2rem;
            background: var(--bg-color);
            border-radius: 8px;
            border: 1px solid var(--surface-border);
        }

        .schedule-time-label {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.76rem;
            font-weight: 700;
            color: var(--text-muted);
            background: var(--bg-color);
            border-radius: 8px;
            border: 1px solid var(--surface-border);
            padding: 0.4rem;
            text-align: center;
        }

        .schedule-slot {
            background-color: var(--bg-color);
            border: 1px solid var(--surface-border);
            border-radius: 10px;
            min-height: 60px;
            padding: 0.4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            outline: none;
            color: var(--text-color);
            font-size: 0.8rem;
            text-align: center;
        }

        .schedule-slot:hover {
            border-color: var(--primary-color);
            background-color: var(--card-sky);
            transform: translateY(-1px);
        }

        .schedule-slot:focus {
            border-color: var(--primary-color);
            background-color: var(--card-lavender);
        }

        .schedule-recess-row {
            grid-column: 1 / -1;
            background-color: var(--card-amber);
            border: 1px dashed var(--border-amber);
            border-radius: 8px;
            padding: 0.5rem;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
        }

        /* --- CALENDARIO MENSUAL TRADICIONAL --- */
        .calendar-container {
            max-width: 1050px;
            margin: 2.2rem auto;
            padding: 0 1.5rem;
        }

        .calendar-card {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 20px;
            padding: 1.8rem;
            box-shadow: var(--shadow);
        }

        .calendar-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.4rem;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .calendar-title-group {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .calendar-title-group h3 {
            font-size: 1.35rem;
            font-weight: 800;
            min-width: 220px;
            text-transform: capitalize;
        }

        .calendar-nav-btn {
            background-color: var(--bg-color);
            border: 1px solid var(--surface-border);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .calendar-nav-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-today {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
        }

        .btn-today:hover {
            background-color: var(--primary-hover);
        }

        .weekday-row {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 8px;
        }

        .weekday-row span {
            text-align: center;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .calendar-day {
            position: relative;
            aspect-ratio: 1 / 0.85;
            border-radius: 12px;
            border: 1px solid var(--surface-border);
            background-color: var(--bg-color);
            cursor: pointer;
            padding: 0.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .calendar-day:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .calendar-day.empty {
            background: transparent;
            border-color: transparent;
            cursor: default;
        }

        .calendar-day.empty:hover {
            transform: none;
        }

        .calendar-day.today {
            border: 2px solid var(--primary-color);
            background-color: var(--card-lavender);
        }

        .calendar-day.selected {
            background-color: var(--card-sky);
            border-color: var(--accent-blue);
        }

        .day-event-dots {
            display: flex;
            gap: 3px;
            flex-wrap: wrap;
        }

        .event-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--accent-purple);
        }

        /* --- PANEL DE TAREAS DEL DÍA --- */
        .day-panel {
            margin-top: 1.6rem;
            background-color: var(--card-sky);
            border: 1px solid var(--border-sky);
            border-radius: 18px;
            padding: 1.5rem;
        }

        .day-panel h4 {
            font-size: 1.05rem;
            margin-bottom: 1rem;
            color: var(--text-color);
            text-transform: capitalize;
        }

        .day-input-group {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .day-input-group input {
            flex: 1;
            padding: 0.65rem 0.9rem;
            border: 1px solid var(--border-sky);
            border-radius: 10px;
            background-color: var(--surface-color);
            color: var(--text-color);
            outline: none;
            font-size: 0.92rem;
        }

        .day-input-group input:focus {
            border-color: var(--primary-color);
        }

        .btn-add-event {
            background-color: var(--accent-blue);
            color: #ffffff;
            border: none;
            padding: 0.65rem 1.1rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-add-event:hover {
            background-color: #0369a1;
        }

        .day-event-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .day-event-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0.9rem;
            background-color: var(--surface-color);
            border-radius: 8px;
            font-size: 0.88rem;
            border: 1px solid rgba(0,0,0,0.04);
        }

        .btn-del-event {
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .empty-day-msg {
            color: var(--text-muted);
            font-size: 0.88rem;
            font-style: italic;
        }

        /* --- GALERÍA DE INSPIRACIÓN --- */
        .resources-section {
            max-width: 1050px;
            margin: 1.5rem auto 2.5rem auto;
            padding: 0 1.5rem;
        }

        .section-title {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 1.8rem;
            color: var(--text-color);
            font-weight: 800;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.8rem;
        }

        .feature-card-btn {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 20px;
            overflow: hidden;
            text-align: left;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow);
            position: relative;
        }

        .feature-card-btn:hover {
            transform: translateY(-6px);
            border-color: var(--primary-color);
            box-shadow: 0 15px 30px -10px rgba(99, 102, 241, 0.25);
        }

        .card-img-wrapper {
            position: relative;
            height: 160px;
            width: 100%;
            overflow: hidden;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .feature-card-btn:hover .card-img-wrapper img {
            transform: scale(1.08);
        }

        .card-body {
            padding: 1.3rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex: 1;
        }

        .card-body h4 {
            font-size: 1.08rem;
            color: var(--text-color);
            font-weight: 700;
        }

        .card-body p {
            font-size: 0.86rem;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* --- FOOTER --- */
        .footer {
            background-color: var(--surface-color);
            border-top: 1px solid var(--surface-border);
            padding: 2rem 1rem 1.5rem 1rem;
            text-align: center;
            margin-top: auto;
        }

        .footer-quote-container {
            max-width: 600px;
            margin: 0 auto 1.5rem auto;
            padding: 1.2rem;
            background-color: var(--card-lavender);
            border-left: 4px solid var(--primary-color);
            border-radius: 14px;
        }

        .footer-quote-container h3 {
            font-size: 0.78rem;
            color: var(--primary-color);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        #daily-quote {
            font-size: 0.98rem;
            color: var(--text-color);
            font-style: italic;
            margin-bottom: 0.8rem;
        }

        .btn-refresh-quote {
            background: var(--surface-color);
            border: 1px solid var(--surface-border);
            color: var(--text-muted);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-refresh-quote:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .footer-bottom {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* --- RESPONSIVE DESIGN --- */
        @media (max-width: 900px) {
            .top-hero-carousel {
                height: 260px;
            }

            .hero-overlay-content h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            .navbar {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: var(--surface-color);
                flex-direction: column;
                padding: 1rem;
                border-bottom: 1px solid var(--surface-border);
            }

            .navbar.open {
                display: flex;
            }

            .calendar-day {
                font-size: 0.7rem;
                padding: 0.3rem;
            }

            .weekday-row span {
                font-size: 0.65rem;
            }
        }
    </style>
</head>
<body>

    <div class="focus-banner" id="focus-banner">
        <span>🔕 Modo "No Molestar" Activado — Tu espacio libre de distracciones</span>
        <button class="btn-exit-focus" id="exit-focus-btn">Salir de Modo Foco</button>
    </div>

    <div id="toast-container"></div>

    <header class="navbar-header hide-on-focus">
        <div class="logo">
            <h1>Study<span>Boost</span></h1>
        </div>

        <div class="nav-controls">
            <button class="action-btn" id="focus-btn" title="Modo No Molestar">
                <span>🔕</span> <small>No Molestar</small>
            </button>

            <button class="action-btn" id="theme-btn" title="Cambiar tema visual">
                <span id="theme-icon">🌙</span>
            </button>

            <button class="menu-toggle" id="menu-toggle" aria-label="Abrir Menú">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <nav class="navbar" id="navbar">
            <a href="introduccion.php" class="nav-btn">Inicio</a>
            <a href="tecnicas.html" class="nav-btn">Técnicas</a>
            <a href="calendario.php" class="nav-btn active">Calendario</a>
            <a href="videos.html" class="nav-btn">Videos</a>
            <a href="recordatorio.php" class="nav-btn">Recordatorio</a>
            <a href="metas.php" class="nav-btn">Metas</a>
        </nav>
    </header>

    <main>
        <div class="back-bar hide-on-focus">
            <button class="action-btn btn-back" id="btn-back">
                <span>⬅</span> <small>Volver</small>
            </button>
        </div>

        <section class="top-hero-carousel hide-on-focus">
            <img id="hero-bg-img" class="hero-slide-img" src="" alt="Fondo de Planificación">

            <div class="hero-overlay-content">
                <span class="hero-title-badge">Organización Semanal</span>
                <h2 id="hero-title">Tu Calendario de Estudio</h2>
                <p id="hero-desc">Planifica tus sesiones, marca tus entregas y visualiza tu semana de un vistazo.</p>
            </div>

            <div class="carousel-dots" id="hero-dots"></div>
        </section>

        <!-- HORARIO SEMANAL DE 7 DÍAS CON BLOQUES DE 40 MINUTOS -->
        <section class="weekly-schedule-container">
            <div class="schedule-card">
                <h3 class="schedule-header-title">Horario Semanal Extendido</h3>
                <p class="schedule-subtitle">Distribución en los 7 días de la semana con bloques de 40 minutos (de 08:00 a 13:40, sin contar el receso).</p>

                <div class="schedule-grid-table">
                    <!-- Cabecera de Días -->
                    <div class="schedule-col-header">Hora</div>
                    <div class="schedule-col-header">Lunes</div>
                    <div class="schedule-col-header">Martes</div>
                    <div class="schedule-col-header">Miércoles</div>
                    <div class="schedule-col-header">Jueves</div>
                    <div class="schedule-col-header">Viernes</div>
                    <div class="schedule-col-header">Sábado</div>
                    <div class="schedule-col-header">Domingo</div>

                    <!-- Fila 1: 08:00 - 08:40 -->
                    <div class="schedule-time-label">08:00 - 08:40</div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>

                    <!-- Fila 2: 08:40 - 09:20 -->
                    <div class="schedule-time-label">08:40 - 09:20</div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>

                    <!-- Fila 3: 09:20 - 10:00 -->
                    <div class="schedule-time-label">09:20 - 10:00</div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>

                    <!-- Fila 4: 10:00 - 10:40 -->
                    <div class="schedule-time-label">10:00 - 10:40</div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>

                    <!-- RECREO (10:40 - 11:00) -->
                    <div class="schedule-recess-row">☕ RECREO (10:40 - 11:00)</div>

                    <!-- Fila 5: 11:00 - 11:40 -->
                    <div class="schedule-time-label">11:00 - 11:40</div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>

                    <!-- Fila 6: 11:40 - 12:20 -->
                    <div class="schedule-time-label">11:40 - 12:20</div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>

                    <!-- Fila 7: 12:20 - 13:00 -->
                    <div class="schedule-time-label">12:20 - 13:00</div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>

                    <!-- Fila 8: 13:00 - 13:40 -->
                    <div class="schedule-time-label">13:00 - 13:40</div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                    <div class="schedule-slot" contenteditable="true" placeholder="Escribe aquí..."></div>
                </div>
            </div>
        </section>

        <!-- CALENDARIO MENSUAL TRADICIONAL -->
        <section class="calendar-container">
            <div class="calendar-card">
                <div class="calendar-toolbar">
                    <div class="calendar-title-group">
                        <button class="calendar-nav-btn" id="prev-month-btn" aria-label="Mes anterior">‹</button>
                        <h3 id="calendar-title"></h3>
                        <button class="calendar-nav-btn" id="next-month-btn" aria-label="Mes siguiente">›</button>
                    </div>
                    <button class="btn-today" id="today-btn">Ir a Hoy</button>
                </div>

                <div class="weekday-row">
                    <span>Dom</span><span>Lun</span><span>Mar</span><span>Mié</span><span>Jue</span><span>Vie</span><span>Sáb</span>
                </div>

                <div class="calendar-grid" id="calendar-grid"></div>

                <div class="day-panel" id="day-panel">
                    <h4 id="day-panel-title">Selecciona un día para ver o agregar tareas</h4>
                    <div class="day-input-group">
                        <input type="text" id="event-input" placeholder="Ej: Repasar Tema 3 de Sistemas Informáticos...">
                        <button class="btn-add-event" id="add-event-btn">Agregar</button>
                    </div>
                    <ul class="day-event-list" id="day-event-list"></ul>
                </div>
            </div>
        </section>

        <!-- GALERÍA DE INSPIRACIÓN -->
        <section class="resources-section hide-on-focus">
            <h3 class="section-title">Inspiración para Planificar tu Semana</h3>

            <div class="cards-grid">
                <div class="feature-card-btn">
                    <div class="card-img-wrapper">
                        <img id="img-planifica" src="" alt="Planifica tu semana">
                    </div>
                    <div class="card-body">
                        <h4>Planifica con Anticipación</h4>
                        <p>Dedica 10 minutos cada domingo para distribuir tus tareas de la semana en el calendario y evitar sorpresas de última hora.</p>
                    </div>
                </div>

                <div class="feature-card-btn">
                    <div class="card-img-wrapper">
                        <img id="img-prioriza" src="" alt="Prioriza tus tareas">
                    </div>
                    <div class="card-body">
                        <h4>Prioriza lo Importante</h4>
                        <p>Marca primero las fechas de exámenes y entregas. Lo urgente no siempre es lo más importante para tu aprendizaje.</p>
                    </div>
                </div>

                <div class="feature-card-btn">
                    <div class="card-img-wrapper">
                        <img id="img-cumple" src="" alt="Cumple tus metas">
                    </div>
                    <div class="card-body">
                        <h4>Cumple tus Metas</h4>
                        <p>Revisa tu calendario cada noche y celebra cada tarea completada; los pequeños avances construyen grandes resultados.</p>
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

            // 0. SISTEMA DE TOASTS
            function showToast(message) {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = 'toast';
                toast.textContent = message;
                container.appendChild(toast);
                setTimeout(() => { toast.remove(); }, 3000);
            }

            // 1. GESTIÓN DE TEMA Y MODO NO MOLESTAR
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
                if (notify) showToast(`Modo ${theme === 'light' ? 'Claro Suave' : 'Oscuro Soft'} activado`);
            }

            const focusBtn = document.getElementById('focus-btn');
            const exitFocusBtn = document.getElementById('exit-focus-btn');

            focusBtn.addEventListener('click', () => {
                document.body.classList.add('focus-mode');
                showToast('Modo No Molestar Activado 🔕');
            });

            exitFocusBtn.addEventListener('click', () => {
                document.body.classList.remove('focus-mode');
                showToast('Has vuelto al modo normal 🔔');
            });

            // MENÚ RESPONSIVE
            const menuToggle = document.getElementById('menu-toggle');
            const navbar = document.getElementById('navbar');
            menuToggle.addEventListener('click', () => navbar.classList.toggle('open'));

            // BOTÓN VOLVER A LA PÁGINA ANTERIOR
            document.getElementById('btn-back').addEventListener('click', () => {
                if (document.referrer && document.referrer !== window.location.href) {
                    window.history.back();
                } else {
                    window.location.href = 'introduccion.php';
                }
            });

            // 2. CARRUSEL SUPERIOR (CAMBIO CADA 5 SEGUNDOS)
            const heroSlides = [
                {
                    url: 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?auto=format&fit=crop&w=1200&q=80',
                    title: 'Tu Calendario de Estudio',
                    desc: 'Planifica tus sesiones, marca tus entregas y visualiza tu semana de un vistazo.'
                },
                {
                    url: 'https://images.unsplash.com/photo-1517842645767-c639042777db?auto=format&fit=crop&w=1200&q=80',
                    title: 'Cada Día Cuenta',
                    desc: 'Distribuye tus tareas con anticipación y evita el estrés de última hora.'
                },
                {
                    url: 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?auto=format&fit=crop&w=1200&q=80',
                    title: 'Constancia sobre Perfección',
                    desc: 'Pequeños bloques de estudio organizados valen más que una maratón sin plan.'
                }
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

            setInterval(() => {
                heroIdx = (heroIdx + 1) % heroSlides.length;
                setHeroSlide(heroIdx);
            }, 5000);

            // 3. IMÁGENES ROTATIVAS DE LA GALERÍA DE INSPIRACIÓN
            const planificaImages = [
                'https://images.unsplash.com/photo-1611224923853-80b023f02d71?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1495364141860-b0d03eccd065?auto=format&fit=crop&w=600&q=80'
            ];
            const priorizaImages = [
                'https://images.unsplash.com/photo-1512314889357-e157c22f938d?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1454165833767-027ab6ce1f24?auto=format&fit=crop&w=600&q=80'
            ];
            const cumpleImages = [
                'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80'
            ];

            let cardImgIdx = 0;
            const imgPlanificaEl = document.getElementById('img-planifica');
            const imgPrioriazaEl = document.getElementById('img-prioriza');
            const imgCumpleEl = document.getElementById('img-cumple');

            function updateCardImages() {
                imgPlanificaEl.src = planificaImages[cardImgIdx % planificaImages.length];
                imgPrioriazaEl.src = priorizaImages[cardImgIdx % priorizaImages.length];
                imgCumpleEl.src = cumpleImages[cardImgIdx % cumpleImages.length];
                cardImgIdx++;
            }

            updateCardImages();
            setInterval(updateCardImages, 5000);

            // 4. FRASES MOTIVADORAS
            const quotes = [
                '"El éxito es la suma de pequeños esfuerzos repetidos día tras día." — Robert Collier',
                '"La disciplina es el puente entre tus metas y tus logros." — Jim Rohn',
                '"El secreto para salir adelante es simplemente empezar." — Mark Twain',
                '"Haz de cada día tu obra maestra." — John Wooden',
                '"Un plan de estudio bien organizado vale más que mil horas de esfuerzo sin rumbo." — Anónimo',
                '"No cuentes los días, haz que los días cuenten." — Muhammad Ali'
            ];

            const dailyQuoteEl = document.getElementById('daily-quote');
            const refreshQuoteBtn = document.getElementById('refresh-quote-btn');

            function setRandomQuote() {
                dailyQuoteEl.textContent = quotes[Math.floor(Math.random() * quotes.length)];
            }

            refreshQuoteBtn.addEventListener('click', setRandomQuote);
            setRandomQuote();

            // 5. CALENDARIO MENSUAL FUNCIONAL
            const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

            const today = new Date();
            let viewYear = today.getFullYear();
            let viewMonth = today.getMonth();
            let selectedDateKey = null;

            const calendarTitle = document.getElementById('calendar-title');
            const calendarGrid = document.getElementById('calendar-grid');
            const dayPanel = document.getElementById('day-panel');
            const dayPanelTitle = document.getElementById('day-panel-title');
            const eventInput = document.getElementById('event-input');
            const addEventBtn = document.getElementById('add-event-btn');
            const dayEventList = document.getElementById('day-event-list');

            let calendarEvents = JSON.parse(localStorage.getItem('sb-calendar-events')) || {};

            function saveEvents() {
                localStorage.setItem('sb-calendar-events', JSON.stringify(calendarEvents));
            }

            function dateKey(year, month, day) {
                return `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            }

            function renderCalendar() {
                calendarTitle.textContent = `${monthNames[viewMonth]}${viewYear}`;
                calendarGrid.innerHTML = '';

                const firstDayIndex = new Date(viewYear, viewMonth, 1).getDay();
                const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

                for (let i = 0; i < firstDayIndex; i++) {
                    const empty = document.createElement('div');
                    empty.className = 'calendar-day empty';
                    calendarGrid.appendChild(empty);
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const key = dateKey(viewYear, viewMonth, day);
                    const cell = document.createElement('div');
                    cell.className = 'calendar-day';

                    const isToday = viewYear === today.getFullYear() && viewMonth === today.getMonth() && day === today.getDate();
                    if (isToday) cell.classList.add('today');
                    if (key === selectedDateKey) cell.classList.add('selected');

                    const numberSpan = document.createElement('span');
                    numberSpan.textContent = day;

                    const dotsWrap = document.createElement('div');
                    dotsWrap.className = 'day-event-dots';
                    const dayEvents = calendarEvents[key] || [];
                    dayEvents.slice(0, 4).forEach(() => {
                        const dot = document.createElement('span');
                        dot.className = 'event-dot';
                        dotsWrap.appendChild(dot);
                    });

                    cell.appendChild(numberSpan);
                    cell.appendChild(dotsWrap);

                    cell.addEventListener('click', () => selectDay(key, day));

                    calendarGrid.appendChild(cell);
                }
            }

            function selectDay(key, day) {
                selectedDateKey = key;
                renderCalendar();
                dayPanelTitle.textContent = `Tareas para el ${day} de${monthNames[viewMonth]}`;
                renderDayEvents();
                eventInput.focus();
            }

            function renderDayEvents() {
                dayEventList.innerHTML = '';
                if (!selectedDateKey) return;

                const events = calendarEvents[selectedDateKey] || [];

                if (events.length === 0) {
                    const msg = document.createElement('li');
                    msg.className = 'empty-day-msg';
                    msg.textContent = 'Aún no tienes tareas para este día.';
                    dayEventList.appendChild(msg);
                    return;
                }

                events.forEach((text, index) => {
                    const li = document.createElement('li');
                    li.className = 'day-event-item';
                    li.innerHTML = `
                        <span>${text}</span>
                        <button class="btn-del-event" data-index="${index}">🗑</button>
                    `;
                    dayEventList.appendChild(li);
                });

                dayEventList.querySelectorAll('.btn-del-event').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const idx = parseInt(e.currentTarget.getAttribute('data-index'), 10);
                        calendarEvents[selectedDateKey].splice(idx, 1);
                        if (calendarEvents[selectedDateKey].length === 0) delete calendarEvents[selectedDateKey];
                        saveEvents();
                        renderDayEvents();
                        renderCalendar();
                        showToast('Tarea eliminada');
                    });
                });
            }

            addEventBtn.addEventListener('click', () => {
                if (!selectedDateKey) {
                    showToast('Primero selecciona un día en el calendario');
                    return;
                }
                const val = eventInput.value.trim();
                if (val) {
                    if (!calendarEvents[selectedDateKey]) calendarEvents[selectedDateKey] = [];
                    calendarEvents[selectedDateKey].push(val);
                    saveEvents();
                    eventInput.value = '';
                    renderDayEvents();
                    renderCalendar();
                    showToast('Tarea guardada en el calendario 📅');
                }
            });

            eventInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') addEventBtn.click();
            });

            document.getElementById('prev-month-btn').addEventListener('click', () => {
                viewMonth--;
                if (viewMonth < 0) { viewMonth = 11; viewYear--; }
                renderCalendar();
            });

            document.getElementById('next-month-btn').addEventListener('click', () => {
                viewMonth++;
                if (viewMonth > 11) { viewMonth = 0; viewYear++; }
                renderCalendar();
            });

            document.getElementById('today-btn').addEventListener('click', () => {
                viewYear = today.getFullYear();
                viewMonth = today.getMonth();
                const key = dateKey(viewYear, viewMonth, today.getDate());
                renderCalendar();
                selectDay(key, today.getDate());
            });

            renderCalendar();
        });
    </script>
</body>
</html>