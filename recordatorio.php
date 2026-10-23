<?php
// Llamada al archivo de conexión con la base de datos
require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Boost | Gestión de Recordatorios y Alertas de Estudio</title>
    <style>
        /* --- PALETA DE COLORES Y ESTILOS BASE --- */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, Roboto, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }

        :root[data-theme="light"] {
            --bg-color: #f1f5f9;
            --surface-color: #ffffff;
            --surface-border: #e2e8f0;
            --text-color: #0f172a;
            --text-muted: #475569;
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --primary-light: #eef2ff;
            --accent-purple: #8b5cf6;
            --card-hover-bg: #f8fafc;
            --shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.15);
            --shadow-hover: 0 20px 30px -10px rgba(99, 102, 241, 0.22);
            --badge-bg: #e0e7ff;
            --badge-text: #3730a3;
            --danger-color: #ef4444;
            --danger-light: #fee2e2;
            --success-color: #10b981;
            --success-light: #d1fae5;
        }

        :root[data-theme="dark"] {
            --bg-color: #0f172a;
            --surface-color: #1e293b;
            --surface-border: #334155;
            --text-color: #f8fafc;
            --text-muted: #94a3b8;
            --primary-color: #818cf8;
            --primary-hover: #6366f1;
            --primary-light: #1e1b4b;
            --accent-purple: #c084fc;
            --card-hover-bg: #243347;
            --shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.3);
            --shadow-hover: 0 20px 35px -10px rgba(129, 140, 248, 0.3);
            --badge-bg: #312e81;
            --badge-text: #c7d2fe;
            --danger-color: #f87171;
            --danger-light: #450a0a;
            --success-color: #34d399;
            --success-light: #064e3b;
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

        /* --- MODO NO MOLESTAR --- */
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
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
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
            backdrop-filter: blur(10px);
        }

        .logo h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-color);
            letter-spacing: -0.5px;
        }

        .logo span {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-purple));
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
            background-color: var(--primary-light);
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
        }

        .action-btn:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
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

        /* --- CONTENEDOR PRINCIPAL --- */
        main {
            flex: 1;
            max-width: 1180px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
            width: 100%;
        }

        .top-bar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .btn-back {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            color: var(--text-color);
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: var(--shadow);
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: var(--primary-color);
            color: #ffffff;
            border-color: var(--primary-color);
            transform: translateX(-3px);
        }

        .hero-banner {
            text-align: center;
            padding: 2.5rem 1.5rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(139, 92, 246, 0.08) 100%);
            border-radius: 24px;
            border: 1px solid var(--surface-border);
            margin-bottom: 2.5rem;
        }

        .hero-banner h2 {
            font-size: 2.3rem;
            font-weight: 800;
            margin-bottom: 0.8rem;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, var(--text-color), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-banner p {
            font-size: 1.05rem;
            color: var(--text-muted);
            max-width: 780px;
            margin: 0 auto 1.5rem auto;
            line-height: 1.7;
        }

        /* --- WIDGET DE FRASES MOTIVADORAS --- */
        .motivation-widget {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 24px;
            padding: 1.5rem 2rem;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .motivation-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .motivation-icon {
            font-size: 2rem;
            background: var(--primary-light);
            padding: 0.8rem;
            border-radius: 16px;
        }

        .motivation-text h4 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary-color);
            margin-bottom: 0.2rem;
            font-weight: 700;
        }

        .motivation-text p {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-color);
            font-style: italic;
        }

        .btn-refresh-quote {
            background: var(--bg-color);
            border: 1px solid var(--surface-border);
            color: var(--text-color);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-refresh-quote:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background-color: var(--primary-light);
        }

        /* --- ESTADÍSTICAS RÁPIDAS --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            font-size: 1.8rem;
            background: var(--primary-light);
            padding: 0.6rem;
            border-radius: 12px;
        }

        .stat-info span {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .stat-info h3 {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text-color);
        }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
        }

        /* --- DISEÑO PRINCIPAL EN BLOQUES UNIFORMES Y AMPLIOS --- */
        .reminder-layout {
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        .reminder-form-card,
        .reminder-list-section {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
            width: 100%;
        }

        .reminder-form-card:hover,
        .reminder-list-section:hover {
            box-shadow: var(--shadow-hover);
        }

        .section-header-title {
            font-size: 1.4rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.8rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--surface-border);
        }

        .form-grid-layout {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.2rem;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.span-2 {
            grid-column: span 2;
        }

        .form-group.span-3 {
            grid-column: span 3;
        }

        @media (max-width: 900px) {
            .form-grid-layout {
                grid-template-columns: 1fr;
            }
            .form-group.span-2,
            .form-group.span-3 {
                grid-column: span 1;
            }
        }

        .form-group label {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            padding: 0.78rem 1rem;
            background-color: var(--bg-color);
            border: 1px solid var(--surface-border);
            border-radius: 12px;
            color: var(--text-color);
            font-size: 0.95rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            background-color: var(--surface-color);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .btn-submit-container {
            grid-column: span 3;
            margin-top: 0.5rem;
        }

        @media (max-width: 900px) {
            .btn-submit-container {
                grid-column: span 1;
            }
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-purple));
            color: #ffffff;
            border: none;
            padding: 0.9rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-submit:hover {
            opacity: 0.92;
            transform: translateY(-2px);
        }

        .list-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--surface-border);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .list-header-row h3 {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .reminder-filters {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-pill {
            background-color: var(--bg-color);
            border: 1px solid var(--surface-border);
            color: var(--text-muted);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
        }

        .filter-pill.active {
            background-color: var(--primary-color);
            color: #ffffff;
            border-color: var(--primary-color);
        }

        .list-controls-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 220px;
            padding: 0.65rem 1rem;
            background-color: var(--bg-color);
            border: 1px solid var(--surface-border);
            border-radius: 12px;
            color: var(--text-color);
            font-size: 0.9rem;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .btn-clear-completed {
            background: var(--danger-light);
            border: 1px solid var(--danger-color);
            color: var(--danger-color);
            padding: 0.6rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-clear-completed:hover {
            opacity: 0.85;
        }

        .reminders-container {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 0.3rem;
        }

        .reminder-card {
            background-color: var(--bg-color);
            border: 1px solid var(--surface-border);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .reminder-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .reminder-card.completed {
            opacity: 0.6;
            text-decoration: line-through;
            border-left: 6px solid var(--success-color);
        }

        .reminder-card.urgent {
            border-left: 6px solid var(--danger-color);
        }

        .reminder-card.normal {
            border-left: 6px solid var(--primary-color);
        }

        .reminder-info {
            flex: 1;
        }

        .reminder-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .reminder-title-text {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 0.3rem;
        }

        .reminder-desc-text {
            font-size: 0.92rem;
            color: var(--text-muted);
        }

        .reminder-actions {
            display: flex;
            gap: 0.6rem;
        }

        .btn-action-icon {
            background: var(--surface-color);
            border: 1px solid var(--surface-border);
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .btn-action-icon:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .btn-action-icon.delete:hover {
            border-color: var(--danger-color);
            color: var(--danger-color);
            background-color: var(--danger-light);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }

        .empty-state img {
            width: 140px;
            opacity: 0.7;
            margin-bottom: 1rem;
            border-radius: 12px;
        }

        /* --- FOOTER --- */
        .footer {
            background-color: var(--surface-color);
            border-top: 1px solid var(--surface-border);
            padding: 2rem 1.5rem;
            text-align: center;
            margin-top: 4rem;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .motivation-widget {
                flex-direction: column;
                align-items: flex-start;
            }
            .btn-refresh-quote {
                width: 100%;
                text-align: center;
            }
            .menu-toggle { display: flex; }
            .navbar {
                display: none;
                position: absolute;
                top: 100%; left: 0; width: 100%;
                background-color: var(--surface-color);
                flex-direction: column;
                padding: 1rem;
                border-bottom: 1px solid var(--surface-border);
                box-shadow: var(--shadow);
            }
            .navbar.open { display: flex; }
        }
    </style>
</head>
<body>

    <div class="focus-banner" id="focus-banner">
        <span>🔕 Modo "No Molestar" Activado — Espacio libre de distracciones</span>
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
                <span></span><span></span><span></span>
            </button>
        </div>

        <nav class="navbar" id="navbar">
            <a href="introduccion.php" class="nav-btn">Inicio</a>
            <a href="tecnicas.html" class="nav-btn">Técnicas</a>
            <a href="calendario.php" class="nav-btn">Calendario</a>
            <a href="videos.html" class="nav-btn">Videos</a>
            <a href="recordatorio.php" class="nav-btn active">Recordatorio</a>
            <a href="metas.php" class="nav-btn">Metas</a>
        </nav>
    </header>

    <main>
        <!-- BARRA SUPERIOR CON BOTÓN VOLVER -->
        <div class="top-bar-nav hide-on-focus">
            <a href="tecnicas.php" class="btn-back">
                <span>←</span> Volver a Técnicas
            </a>
        </div>

        <section class="hero-banner hide-on-focus">
            <h2>Gestor de Recordatorios y Alertas de Estudio</h2>
            <p>
                Como estudiante del BTH en Sistemas Informáticos, diseñé este módulo de alertas inteligentes para garantizar que nunca olvides un examen, entrega de proyecto o sesión de repaso con <strong>Study Boost</strong>.
            </p>
        </section>

        <!-- WIDGET DE FRASES MOTIVADORAS -->
        <div class="motivation-widget hide-on-focus">
            <div class="motivation-content">
                <div class="motivation-icon">🔔</div>
                <div class="motivation-text">
                    <h4>Frase Motivadora del Día</h4>
                    <p id="daily-quote">"La disciplina es el puente entre tus metas y tus logros." — Jim Rohn</p>
                </div>
            </div>
            <button class="btn-refresh-quote" id="refresh-quote-btn">Nueva Frase 🔄</button>
        </div>

        <!-- ESTADÍSTICAS RÁPIDAS -->
        <div class="stats-grid hide-on-focus">
            <div class="stat-card">
                <div class="stat-icon">📌</div>
                <div class="stat-info">
                    <span>Total Alertas</span>
                    <h3 id="stat-total">0</h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-info">
                    <span>Pendientes</span>
                    <h3 id="stat-pending">0</h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <span>Completadas</span>
                    <h3 id="stat-completed">0</h3>
                </div>
            </div>
        </div>

        <!-- CONTENEDOR PRINCIPAL: BLOQUES UNIFORMES Y DISEÑADOS SIN ESPACIOS VACÍOS -->
        <div class="reminder-layout">
            <!-- BLOQUE 1: FORMULARIO DE NUEVO RECORDATORIO -->
            <div class="reminder-form-card">
                <h3 class="section-header-title">📝 Nuevo Recordatorio</h3>
                <form id="reminderForm">
                    <div class="form-grid-layout">
                        <div class="form-group span-2">
                            <label for="remTitle">Título del Recordatorio</label>
                            <input type="text" id="remTitle" class="form-control" placeholder="Ej. Repasar Técnica Feynman" required>
                        </div>

                        <div class="form-group">
                            <label for="remPriority">Nivel de Prioridad</label>
                            <select id="remPriority" class="form-control">
                                <option value="normal">⚡ Normal</option>
                                <option value="urgent">🔥 Urgente</option>
                            </select>
                        </div>

                        <div class="form-group span-3">
                            <label for="remDate">Fecha y Hora Límite</label>
                            <input type="datetime-local" id="remDate" class="form-control" required>
                        </div>

                        <div class="form-group span-3">
                            <label for="remDesc">Notas / Descripción breve</label>
                            <textarea id="remDesc" class="form-control" placeholder="Detalles opcionales de la tarea o recordatorio..."></textarea>
                        </div>

                        <div class="btn-submit-container">
                            <button type="submit" class="btn-submit">Programar Alerta ⏰</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- BLOQUE 2: LISTA DE ALERTAS ACTIVAS -->
            <div class="reminder-list-section">
                <div class="list-header-row">
                    <h3 class="section-header-title">📋 Mis Alertas Activas</h3>
                    <div class="reminder-filters">
                        <button class="filter-pill active" onclick="filterReminders('all')">Todos</button>
                        <button class="filter-pill" onclick="filterReminders('urgent')">Urgentes</button>
                        <button class="filter-pill" onclick="filterReminders('normal')">Normales</button>
                    </div>
                </div>

                <div class="list-controls-bar">
                    <input type="text" id="searchReminder" class="search-input" placeholder="🔍 Buscar recordatorio por título...">
                    <button class="btn-clear-completed" id="clearCompletedBtn">🗑️ Limpiar completadas</button>
                </div>

                <div class="reminders-container" id="remindersContainer">
                    <!-- Elementos generados por JS -->
                </div>
            </div>
        </div>
    </main>

    <footer class="footer hide-on-focus">
        <p>&copy; 2026 Study Boost — Proyecto de Innovación BTH Sistemas Informáticos. Todos los derechos reservados.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const remDateInput = document.getElementById('remDate');
            if (remDateInput && !remDateInput.value) {
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                remDateInput.value = now.toISOString().slice(0, 16);
            }

            const themeBtn = document.getElementById('theme-btn');
            const themeIcon = document.getElementById('theme-icon');
            const htmlElement = document.documentElement;

            const savedTheme = localStorage.getItem('studyboost_theme') || 'light';
            htmlElement.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);

            themeBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                htmlElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('studyboost_theme', newTheme);
                updateThemeIcon(newTheme);
                showToast(newTheme === 'dark' ? 'Modo Oscuro Activado 🌙' : 'Modo Claro Activado ☀️');
            });

            function updateThemeIcon(theme) {
                themeIcon.textContent = theme === 'dark' ? '☀️' : '🌙';
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

            const menuToggle = document.getElementById('menu-toggle');
            const navbar = document.getElementById('navbar');
            menuToggle.addEventListener('click', () => navbar.classList.toggle('open'));

            const quotes = [
                '"La disciplina es el puente entre tus metas y tus logros." — Jim Rohn',
                '"El éxito es la suma de pequeños esfuerzos repetidos día tras día." — Robert Collier',
                '"Haz de cada día tu obra maestra." — John Wooden',
                '"El secreto para salir adelante es simplemente empezar." — Mark Twain',
                '"No cuentes los días, haz que los días cuenten." — Muhammad Ali'
            ];

            const dailyQuoteEl = document.getElementById('daily-quote');
            const refreshQuoteBtn = document.getElementById('refresh-quote-btn');

            function setRandomQuote() {
                dailyQuoteEl.textContent = quotes[Math.floor(Math.random() * quotes.length)];
                showToast('Nueva frase cargada ✨');
            }

            refreshQuoteBtn.addEventListener('click', setRandomQuote);

            let reminders = JSON.parse(localStorage.getItem('studyboost_reminders')) || [
                { id: 1, title: 'Examen Final de Sistemas', date: '2026-06-15T09:00', priority: 'urgent', desc: 'Repasar arquitectura de redes y bases de datos.', completed: false },
                { id: 2, title: 'Sesión Repaso Pomodoro', date: '2026-06-12T16:00', priority: 'normal', desc: 'Completar 4 ciclos pomodoro con la técnica Feynman.', completed: false },
                { id: 3, title: 'Entrega de Proyecto BTH', date: '2026-06-20T23:59', priority: 'urgent', desc: 'Finalizar maquetación HTML/CSS de Study Boost.', completed: true }
            ];

            let currentFilter = 'all';
            let searchQuery = '';

            const reminderForm = document.getElementById('reminderForm');
            const remindersContainer = document.getElementById('remindersContainer');
            const searchInput = document.getElementById('searchReminder');
            const clearCompletedBtn = document.getElementById('clearCompletedBtn');

            function updateStats() {
                const total = reminders.length;
                const completed = reminders.filter(r => r.completed).length;
                const pending = total - completed;

                document.getElementById('stat-total').textContent = total;
                document.getElementById('stat-pending').textContent = pending;
                document.getElementById('stat-completed').textContent = completed;
            }

            function renderReminders() {
                remindersContainer.innerHTML = '';
                
                let filtered = reminders;
                
                if (currentFilter !== 'all') {
                    filtered = filtered.filter(r => r.priority === currentFilter);
                }

                if (searchQuery.trim() !== '') {
                    filtered = filtered.filter(r => r.title.toLowerCase().includes(searchQuery.toLowerCase()) || r.desc.toLowerCase().includes(searchQuery.toLowerCase()));
                }

                updateStats();

                if (filtered.length === 0) {
                    remindersContainer.innerHTML = `
                        <div class="empty-state">
                            <img src="https://images.unsplash.com/photo-1517842645767-c639042777db?auto=format&fit=crop&w=300&q=80" alt="Sin recordatorios">
                            <p>No se encontraron recordatorios con los filtros actuales. ✨</p>
                        </div>
                    `;
                    return;
                }

                filtered.forEach(rem => {
                    const card = document.createElement('div');
                    card.className = `reminder-card ${rem.priority}${rem.completed ? 'completed' : ''}`;
                    
                    const dateFormatted = new Date(rem.date).toLocaleString('es-ES', { dateStyle: 'medium', timeStyle: 'short' });

                    card.innerHTML = `
                        <div class="reminder-info">
                            <div class="reminder-meta">
                                <span>📅 ${dateFormatted}</span>
                                <span>🏷️ ${rem.priority === 'urgent' ? 'Urgente 🔥' : 'Normal ⚡'}</span>
                            </div>
                            <h4 class="reminder-title-text">${rem.title}</h4>
                            <p class="reminder-desc-text">${rem.desc || 'Sin notas adicionales.'}</p>
                        </div>
                        <div class="reminder-actions">
                            <button class="btn-action-icon" title="${rem.completed ? 'Marcar pendiente' : 'Completar'}" onclick="toggleReminder(${rem.id})">
                                ${rem.completed ? '↩️' : '✅'}
                            </button>
                            <button class="btn-action-icon delete" title="Eliminar" onclick="deleteReminder(${rem.id})">
                                🗑️
                            </button>
                        </div>
                    `;
                    remindersContainer.appendChild(card);
                });
            }

            reminderForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const title = document.getElementById('remTitle').value;
                const date = document.getElementById('remDate').value;
                const priority = document.getElementById('remPriority').value;
                const desc = document.getElementById('remDesc').value;

                const newReminder = {
                    id: Date.now(),
                    title,
                    date,
                    priority,
                    desc,
                    completed: false
                };

                reminders.unshift(newReminder);
                localStorage.setItem('studyboost_reminders', JSON.stringify(reminders));
                renderReminders();
                reminderForm.reset();
                
                if (remDateInput) {
                    const now = new Date();
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                    remDateInput.value = now.toISOString().slice(0, 16);
                }

                showToast('Recordatorio programado con éxito ⏰');
            });

            window.toggleReminder = function(id) {
                reminders = reminders.map(r => r.id === id ? { ...r, completed: !r.completed } : r);
                localStorage.setItem('studyboost_reminders', JSON.stringify(reminders));
                renderReminders();
                showToast('Estado de alerta actualizado 🔄');
            };

            window.deleteReminder = function(id) {
                reminders = reminders.filter(r => r.id !== id);
                localStorage.setItem('studyboost_reminders', JSON.stringify(reminders));
                renderReminders();
                showToast('Recordatorio eliminado 🗑️');
            };

            window.filterReminders = function(filter) {
                currentFilter = filter;
                document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
                event.currentTarget.classList.add('active');
                renderReminders();
            };

            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value;
                renderReminders();
            });

            clearCompletedBtn.addEventListener('click', () => {
                const initialCount = reminders.length;
                reminders = reminders.filter(r => !r.completed);
                if(reminders.length < initialCount) {
                    localStorage.setItem('studyboost_reminders', JSON.stringify(reminders));
                    renderReminders();
                    showToast('Alertas completadas eliminadas 🧹');
                } else {
                    showToast('No hay alertas completadas para limpiar ⚠️');
                }
            });

            renderReminders();

            window.showToast = function(message) {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = 'toast';
                toast.textContent = message;
                container.appendChild(toast);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            };
        });
    </script>
</body>
</html>