<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../middleware/auth.php';

// Solo administradores pueden acceder
AuthMiddleware::requireRole('Administrador');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Sistema de Giras UTP</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <!-- HEADER -->
    <header class="admin-header">
        <div class="container">
            <div class="header-content">
                <div>
                    <h1>Panel de Administración</h1>
                    <p class="subtitle user-name">Cargando...</p>
                </div>
                <nav class="nav-menu">
                    <a href="user-dashboard.html">Ver como Usuario</a>
                    <button class="btn-outline" id="btn-logout">Cerrar Sesión</button>
                </nav>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="container">
        <!-- ESTADÍSTICAS -->
        <section class="stats-section">
            <h2>Estadísticas Generales</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3 id="stat-usuarios">0</h3>
                        <p>Total Usuarios</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎓</div>
                    <div class="stat-info">
                        <h3 id="stat-estudiantes">0</h3>
                        <p>Estudiantes</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🔧</div>
                    <div class="stat-info">
                        <h3 id="stat-organizadores">0</h3>
                        <p>Organizadores</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚙️</div>
                    <div class="stat-info">
                        <h3 id="stat-admins">0</h3>
                        <p>Administradores</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🚌</div>
                    <div class="stat-info">
                        <h3 id="stat-giras">0</h3>
                        <p>Giras Registradas</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TABS -->
        <section class="tabs-section">
            <div class="tabs">
                <button class="tab-btn active" data-tab="bitacora">📋 Bitácora de Accesos</button>
                <button class="tab-btn" data-tab="usuarios">👥 Usuarios</button>
                <button class="tab-btn" data-tab="giras">🚌 Giras</button>
                <button class="tab-btn" data-tab="inscripciones">📝 Inscripciones</button>
            </div>

            <!-- TAB CONTENT: BITÁCORA -->
            <div class="tab-content active" id="tab-bitacora">
                <h3>Registro de Accesos al Sistema</h3>
                <div class="table-container">
                    <table id="tabla-bitacora">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Fecha y Hora</th>
                                <th>IP</th>
                                <th>Navegador</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="loading">Cargando datos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB CONTENT: USUARIOS -->
            <div class="tab-content" id="tab-usuarios">
                <h3>Usuarios Registrados</h3>
                <div class="table-container">
                    <table id="tabla-usuarios">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Fecha de Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="loading">Cargando datos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB CONTENT: GIRAS -->
            <div class="tab-content" id="tab-giras">
                <h3>Giras Registradas en el Sistema</h3>
                <div class="table-container">
                    <table id="tabla-giras">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Fecha</th>
                                <th>Lugar</th>
                                <th>Inscritos</th>
                                <th>Organizador ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="loading">Cargando datos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB CONTENT: INSCRIPCIONES -->
            <div class="tab-content" id="tab-inscripciones">
                <h3>Inscripciones a Giras</h3>
                <div class="table-container">
                    <table id="tabla-inscripciones">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Correo</th>
                                <th>Gira</th>
                                <th>Fecha Gira</th>
                                <th>Lugar</th>
                                <th>Estado</th>
                                <th>Fecha Inscripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="loading">Cargando datos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script defer src="../js/admin.js"></script>
</body>
</html>