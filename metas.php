<?php
require_once 'conexion.php'; // Esto llama al archivo de conexión para que esté disponible
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Boost | Panel de Metas y Objetivos Académicos</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
            width: 100%;
        }

        .hero-banner {
            text-align: center;
            padding: 2.5rem 1.5rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(139, 92, 246, 0.08) 100%);
            border-radius: 24px;
            border: 1px solid var(--surface-border);
            margin-bottom: 2rem;
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
            margin: 0 auto;
            line-height: 1.7;
        }

        /* --- SECCIÓN IA EXPANDIDA Y PROTAGONISTA --- */
        .ai-master-section {
            background: linear-gradient(135deg, var(--surface-color), var(--primary-light));
            border: 2px solid var(--primary-color);
            border-radius: 24px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow-hover);
        }

        .ai-master-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .ai-master-title {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-color);
        }

        .ai-master-title span.icon {
            font-size: 2rem;
            background: var(--surface-color);
            padding: 0.4rem 0.8rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .btn-ai-action {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-purple));
            color: #ffffff;
            border: none;
            padding: 0.8rem 1.6rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-ai-action:hover {
            opacity: 0.92;
            transform: translateY(-2px);
        }

        .ai-grid-display {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media(max-width: 850px) {
            .ai-grid-display {
                grid-template-columns: 1fr;
            }
        }

        .ai-card-box {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 18px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
        }

        .ai-card-box h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .ai-card-box p, .ai-card-box ul {
            font-size: 0.95rem;
            color: var(--text-color);
            line-height: 1.6;
        }

        .ai-card-box ul {
            padding-left: 1.2rem;
        }

        /* --- BARRA DE PROGRESO GENERAL --- */
        .progress-overview {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
        }

        .progress-info h4 {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 0.3rem;
        }

        .progress-info p {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .progress-bar-container {
            flex: 1;
            max-width: 400px;
            background-color: var(--bg-color);
            border-radius: 10px;
            height: 14px;
            overflow: hidden;
            border: 1px solid var(--surface-border);
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-purple));
            width: 0%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .progress-percentage {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--primary-color);
            min-width: 60px;
            text-align: right;
        }

        /* --- SECCIONES SEPARADAS INDEPENDIENTES --- */
        .content-sections-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        .goal-form-section {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
        }

        .goal-form-section h3 {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.2rem;
            align-items: end;
        }

        @media (max-width: 950px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
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
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-purple));
            color: #ffffff;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            height: 48px;
        }

        .btn-submit:hover {
            opacity: 0.92;
            transform: translateY(-2px);
        }

        .goal-list-section {
            background-color: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
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
            font-size: 1.4rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .goal-filters {
            display: flex;
            gap: 0.5rem;
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

        .goals-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .goal-card {
            background-color: var(--bg-color);
            border: 1px solid var(--surface-border);
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .goal-card.completed {
            opacity: 0.65;
            border-left: 6px solid var(--success-color);
        }

        .goal-card.active-goal {
            border-left: 6px solid var(--primary-color);
        }

        .goal-info {
            flex: 1;
        }

        .goal-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 0.3rem;
        }

        .goal-title-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 0.2rem;
        }

        .goal-desc-text {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .goal-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action-icon {
            background: var(--surface-color);
            border: 1px solid var(--surface-border);
            width: 40px;
            height: 40px;
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
        }

        .btn-action-icon.delete:hover {
            border-color: #ef4444;
            color: #ef4444;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }

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
            .progress-overview {
                flex-direction: column;
                align-items: flex-start;
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
            <a href="recordatorio.php" class="nav-btn">Recordatorio</a>
            <a href="metas.php" class="nav-btn active">Metas</a>
        </nav>
    </header>

    <main>
        <section class="hero-banner hide-on-focus">
            <h2>Panel de Metas y Objetivos Académicos</h2>
            <p>
                Como estudiante del BTH en Sistemas Informáticos, planificar tus metas a corto y largo plazo es la clave para el éxito en tu proyecto de innovación y tus materias. ¡Define, mide y conquista tus objetivos con <strong>Study Boost</strong>!
            </p>
        </section>

        <!-- SECCIÓN DE IA EXPANDIDA Y PROTAGONISTA -->
        <section class="ai-master-section">
            <div class="ai-master-header">
                <div class="ai-master-title">
                    <span class="icon">✨</span>
                    <div>
                        <h3>Asesor Inteligente Study Boost</h3>
                        <p style="font-size: 0.9rem; color: var(--text-muted); font-weight: normal;">Análisis predictivo de rendimiento y parámetros de mejora académica</p>
                    </div>
                </div>
                <button class="btn-ai-action" id="analyzeGoalsBtn">
                    <span>🤖</span> Ejecutar Diagnóstico IA
                </button>
            </div>

            <div class="ai-grid-display">
                <div class="ai-card-box">
                    <h4>📊 Diagnóstico y Estado Actual</h4>
                    <p id="aiDiagnosisText">Haz clic en <strong>"Ejecutar Diagnóstico IA"</strong> para que el sistema procese tus metas actuales, evalúe tus avances y determine tu nivel de enfoque en el área de sistemas.</p>
                </div>
                <div class="ai-card-box">
                    <h4>🎯 Recomendaciones para Subir Notas</h4>
                    <p id="aiRecommendationsText">Aquí aparecerán las pautas exactas sobre constancia, organización de tiempo, técnicas de estudio recomendadas y áreas críticas a reforzar.</p>
                </div>
            </div>
        </section>

        <!-- BARRA DE PROGRESO GENERAL -->
        <div class="progress-overview">
            <div class="progress-info">
                <h4>Progreso General de Metas</h4>
                <p id="progressText">Completadas 0 de 0 objetivos</p>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progressBarFill"></div>
            </div>
            <div class="progress-percentage" id="progressPercentage">0%</div>
        </div>

        <!-- CONTENEDORES SEPARADOS E INDEPENDIENTES -->
        <div class="content-sections-wrapper">
            
            <!-- BLOQUE 1: NUEVA META ACADÉMICA -->
            <section class="goal-form-section">
                <h3>🎯 Nueva Meta Académica</h3>
                <form id="goalForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="goalTitle">Título del Objetivo</label>
                            <input type="text" id="goalTitle" class="form-control" placeholder="Ej. Dominar el Método Feynman" required>
                        </div>

                        <div class="form-group">
                            <label for="goalCategory">Categoría</label>
                            <select id="goalCategory" class="form-control">
                                <option value="Estudio">🧠 Técnica de Estudio</option>
                                <option value="Proyecto BTH">💻 Proyecto BTH Sistemas</option>
                                <option value="Calificaciones">📚 Calificaciones y Notas</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn-submit">Guardar Meta 🚀</button>
                        </div>

                        <div class="form-group full-width" style="margin-top: 0.5rem;">
                            <label for="goalDesc">Descripción / Plan de Acción detallado</label>
                            <textarea id="goalDesc" class="form-control" rows="2" placeholder="¿Qué pasos específicos seguirás para lograr esta meta?"></textarea>
                        </div>
                    </div>
                </form>
            </section>

            <!-- BLOQUE 2: MIS METAS Y DESAFÍOS -->
            <section class="goal-list-section">
                <div class="list-header-row">
                    <h3>🏆 Mis Metas y Desafíos Activos</h3>
                    <div class="goal-filters">
                        <button class="filter-pill active" onclick="filterGoals('all')">Todas</button>
                        <button class="filter-pill" onclick="filterGoals('active')">Pendientes</button>
                        <button class="filter-pill" onclick="filterGoals('completed')">Completadas</button>
                    </div>
                </div>

                <div class="goals-container" id="goalsContainer">
                    <!-- Elementos generados por JS -->
                </div>
            </section>

        </div>
    </main>

    <footer class="footer hide-on-focus">
        <p>&copy; 2026 Study Boost — Proyecto de Innovación BTH Sistemas Informáticos. Todos los derechos reservados.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
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

            let goals = JSON.parse(localStorage.getItem('studyboost_goals')) || [
                { id: 1, title: 'Terminar maquetación HTML/CSS de Study Boost', category: 'Proyecto BTH', desc: 'Completar todas las páginas del sistema de innovación BTH.', completed: true },
                { id: 2, title: 'Aplicar Repetición Espaciada en Redes', category: 'Estudio', desc: 'Repasar protocolos TCP/IP usando flashcards Leitner.', completed: false },
                { id: 3, title: 'Obtener nota sobresaliente en el trimestre', category: 'Calificaciones', desc: 'Mantener un promedio superior a 90 puntos en todas las materias.', completed: false }
            ];

            let currentFilter = 'all';

            const goalForm = document.getElementById('goalForm');
            const goalsContainer = document.getElementById('goalsContainer');

            function renderGoals() {
                goalsContainer.innerHTML = '';
                
                let filtered = goals;
                if (currentFilter === 'active') {
                    filtered = goals.filter(g => !g.completed);
                } else if (currentFilter === 'completed') {
                    filtered = goals.filter(g => g.completed);
                }

                const total = goals.length;
                const completedCount = goals.filter(g => g.completed).length;
                const percentage = total > 0 ? Math.round((completedCount / total) * 100) : 0;

                document.getElementById('progressText').textContent = `Completadas ${completedCount} de${total} objetivos`;
                document.getElementById('progressBarFill').style.width = `${percentage}%`;
                document.getElementById('progressPercentage').textContent = `${percentage}%`;

                if (filtered.length === 0) {
                    goalsContainer.innerHTML = `
                        <div class="empty-state">
                            <p>No hay metas en esta categoría. ¡Agrega una nueva arriba! 🚀</p>
                        </div>
                    `;
                    return;
                }

                filtered.forEach(goal => {
                    const card = document.createElement('div');
                    card.className = `goal-card ${goal.completed ? 'completed' : 'active-goal'}`;

                    card.innerHTML = `
                        <div class="goal-info">
                            <div class="goal-meta">
                                <span>🏷️ ${goal.category}</span>
                                <span>${goal.completed ? '✅ Completada' : '⏳ En Progreso'}</span>
                            </div>
                            <h4 class="goal-title-text">${goal.title}</h4>
                            <p class="goal-desc-text">${goal.desc || 'Sin plan detallado.'}</p>
                        </div>
                        <div class="goal-actions">
                            <button class="btn-action-icon" title="${goal.completed ? 'Marcar pendiente' : 'Completar'}" onclick="toggleGoal(${goal.id})">
                                ${goal.completed ? '↩️' : '✅'}
                            </button>
                            <button class="btn-action-icon delete" title="Eliminar" onclick="deleteGoal(${goal.id})">
                                🗑️
                            </button>
                        </div>
                    `;
                    goalsContainer.appendChild(card);
                });
            }

            goalForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const title = document.getElementById('goalTitle').value;
                const category = document.getElementById('goalCategory').value;
                const desc = document.getElementById('goalDesc').value;

                const newGoal = {
                    id: Date.now(),
                    title,
                    category,
                    desc,
                    completed: false
                };

                goals.unshift(newGoal);
                localStorage.setItem('studyboost_goals', JSON.stringify(goals));
                renderGoals();
                goalForm.reset();
                showToast('Meta académica guardada con éxito 🎯');
            });

            window.toggleGoal = function(id) {
                goals = goals.map(g => g.id === id ? { ...g, completed: !g.completed } : g);
                localStorage.setItem('studyboost_goals', JSON.stringify(goals));
                renderGoals();
                showToast('Progreso de meta actualizado 🔄');
            };

            window.deleteGoal = function(id) {
                goals = goals.filter(g => g.id !== id);
                localStorage.setItem('studyboost_goals', JSON.stringify(goals));
                renderGoals();
                showToast('Meta eliminada 🗑️');
            };

            window.filterGoals = function(filter) {
                currentFilter = filter;
                document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
                event.currentTarget.classList.add('active');
                renderGoals();
            };

            renderGoals();

            // 5. INTELIGENCIA ARTIFICIAL CONFIGURADA CORRECTAMENTE
            const analyzeGoalsBtn = document.getElementById('analyzeGoalsBtn');
            const aiDiagnosisText = document.getElementById('aiDiagnosisText');
            const aiRecommendationsText = document.getElementById('aiRecommendationsText');

            analyzeGoalsBtn.addEventListener('click', async () => {
                aiDiagnosisText.innerHTML = '🤖 Analizando patrones de estudio y nivel de cumplimiento...';
                aiRecommendationsText.innerHTML = '⏳ Procesando variables de rendimiento académico...';

                const pendingGoals = goals.filter(g => !g.completed);
                const completedGoals = goals.filter(g => g.completed);
                const total = goals.length;
                const completedCount = completedGoals.length;
                const percentage = total > 0 ? Math.round((completedCount / total) * 100) : 0;

                const promptText = `Actúa como un mentor de IA avanzado para un estudiante de sistemas del BTH. 
                Tiene ${completedCount} metas completadas de un total de ${total} (${percentage}% de avance). 
                Metas pendientes: ${JSON.stringify(pendingGoals)}. 
                Dame un diagnóstico detallado en dos partes separadas en español: 
                1. Diagnóstico de su rendimiento actual.
                2. Parámetros exactos que debe mejorar para subir sus calificaciones y asegurar la excelencia en su proyecto.`;

                try {
                    const API_KEY = "AQ.Ab8RN6I6c1j7CKMnZfNlJ1wb4yRRY9arHd43YYUENw1gnAUApg";

                    if(API_KEY === "AQ.Ab8RN6I6c1j7CKMnZfNlJ1wb4yRRY9arHd43YYUENw1gnAUApg") {
                        setTimeout(() => {
                            aiDiagnosisText.innerHTML = `Tienes un total de <strong>${total} objetivos registrados</strong>, con un nivel de avance del <strong>${percentage}%</strong>. Has completado ${completedCount} meta(s) clave, pero mantienes${pendingGoals.length} pendiente(s) que requieren mayor atención temporal.`;
                            
                            aiRecommendationsText.innerHTML = `<ul>
                                <li><strong>Constancia en Sistemas:</strong> Dedica bloques fijos de 45 minutos diarios sin distracciones a tu proyecto BTH.</li>
                                <li><strong>Optimización de Notas:</strong> Prioriza las tareas de la categoría de calificaciones usando la técnica de Estudio Activo.</li>
                                <li><strong>Gestión del tiempo:</strong> Evita la acumulación de pendientes fragmentando los objetivos grandes en micro-tareas diarias.</li>
                            </ul>`;
                        }, 800);
                        return;
                    }

                    const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${API_KEY}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ contents: [{ parts: [{ text: promptText }] }] })
                    });

                    const data = await response.json();
                    const aiText = data.candidates[0].content.parts[0].text;
                    
                    aiDiagnosisText.innerHTML = aiText;
                    aiRecommendationsText.innerHTML = "Análisis en tiempo real generado exitosamente por Google Gemini.";

                } catch (error) {
                    aiDiagnosisText.innerHTML = '⚠️ Error al conectar con el motor de IA. Comprueba tu conexión o tu API Key.';
                    aiRecommendationsText.innerHTML = 'No se pudieron generar recomendaciones automáticas.';
                }
            });

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