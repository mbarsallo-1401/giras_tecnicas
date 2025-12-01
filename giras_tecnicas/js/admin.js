// ---------- VERIFICAR SESIÓN Y PERMISOS ----------
document.addEventListener("DOMContentLoaded", async () => {
    try {
        const sessionResponse = await fetch('../api/session.php');
        const sessionData = await sessionResponse.json();

        if (!sessionData.success) {
            window.location.href = 'login.html';
            return;
        }

        // Verificar que sea administrador
        if (sessionData.data.usuario.rol !== 'Administrador') {
            alert('No tienes permisos para acceder a esta página');
            window.location.href = 'user-dashboard.html';
            return;
        }

        // Mostrar nombre del usuario
        const userNameElement = document.querySelector('.user-name');
        if (userNameElement && sessionData.data.usuario) {
            userNameElement.textContent = `Administrador: ${sessionData.data.usuario.nombre}`;
        }

        // Cargar datos del panel
        await cargarDatosAdmin();

    } catch (error) {
        console.error('Error al verificar sesión:', error);
        window.location.href = 'login.html';
        return;
    }

    // Configurar botón de cerrar sesión
    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.addEventListener('click', async () => {
            try {
                await fetch('../api/logout.php');
                window.location.href = 'login.html';
            } catch (error) {
                console.error('Error al cerrar sesión:', error);
                window.location.href = 'login.html';
            }
        });
    }

    // Configurar tabs
    configurarTabs();
});

// ---------- CONFIGURAR TABS ----------
function configurarTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabName = btn.getAttribute('data-tab');

            // Remover active de todos
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Activar el seleccionado
            btn.classList.add('active');
            document.getElementById(`tab-${tabName}`).classList.add('active');
        });
    });
}

// ---------- CARGAR DATOS DEL ADMIN ----------
async function cargarDatosAdmin() {
    try {
        const response = await fetch('../api/admin.php?tipo=all');
        const result = await response.json();

        if (result.success && result.data) {
            const { usuarios, giras, bitacora, stats } = result.data;

            // Actualizar estadísticas
            document.getElementById('stat-usuarios').textContent = stats.totalUsuarios;
            document.getElementById('stat-estudiantes').textContent = stats.totalEstudiantes;
            document.getElementById('stat-organizadores').textContent = stats.totalOrganizadores;
            document.getElementById('stat-admins').textContent = stats.totalAdministradores;
            document.getElementById('stat-giras').textContent = stats.totalGiras;

            // Llenar tablas
            llenarTablaBitacora(bitacora);
            llenarTablaUsuarios(usuarios);
            llenarTablaGiras(giras);

            // Cargar inscripciones por separado
            await cargarInscripciones();
        }
    } catch (error) {
        console.error('Error al cargar datos:', error);
    }
}

// ---------- LLENAR TABLA BITÁCORA ----------
function llenarTablaBitacora(accesos) {
    const tbody = document.querySelector('#tabla-bitacora tbody');
    tbody.innerHTML = '';

    if (accesos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="loading">No hay registros de acceso</td></tr>';
        return;
    }

    accesos.forEach(acceso => {
        const fecha = new Date(acceso.FechaAcceso).toLocaleString('es-PA');
        const rolBadge = obtenerBadgeRol(acceso.Rol);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${acceso.AccesoID}</td>
            <td>${acceso.Nombre}</td>
            <td>${acceso.Correo}</td>
            <td>${rolBadge}</td>
            <td>${fecha}</td>
            <td>${acceso.IP}</td>
            <td title="${acceso.Navegador}">${truncarTexto(acceso.Navegador, 40)}</td>
        `;
        tbody.appendChild(tr);
    });
}

// ---------- LLENAR TABLA USUARIOS ----------
function llenarTablaUsuarios(usuarios) {
    const tbody = document.querySelector('#tabla-usuarios tbody');
    tbody.innerHTML = '';

    if (usuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="loading">No hay usuarios registrados</td></tr>';
        return;
    }

    usuarios.forEach(usuario => {
        const fecha = new Date(usuario.FechaRegistro).toLocaleString('es-PA');
        const rolBadge = obtenerBadgeRol(usuario.Rol);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${usuario.UsuarioID}</td>
            <td>${usuario.Nombre}</td>
            <td>${usuario.Correo}</td>
            <td>${rolBadge}</td>
            <td>${fecha}</td>
        `;
        tbody.appendChild(tr);
    });
}

// ---------- LLENAR TABLA GIRAS ----------
function llenarTablaGiras(giras) {
    const tbody = document.querySelector('#tabla-giras tbody');
    tbody.innerHTML = '';

    if (giras.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="loading">No hay giras registradas</td></tr>';
        return;
    }

    giras.forEach(gira => {
        const fecha = new Date(gira.Fecha).toLocaleDateString('es-PA');

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${gira.GiraID}</td>
            <td>${gira.Nombre}</td>
            <td>${fecha}</td>
            <td>${gira.Lugar}</td>
            <td>${gira.totalInscritos || 0}</td>
            <td>${gira.OrganizadorID || 'N/A'}</td>
        `;
        tbody.appendChild(tr);
    });
}

// ---------- CARGAR INSCRIPCIONES ----------
async function cargarInscripciones() {
    try {
        const response = await fetch('../api/admin.php?tipo=inscripciones');
        const result = await response.json();

        if (result.success && result.data) {
            llenarTablaInscripciones(result.data);
        }
    } catch (error) {
        console.error('Error al cargar inscripciones:', error);
    }
}

// ---------- LLENAR TABLA INSCRIPCIONES ----------
function llenarTablaInscripciones(inscripciones) {
    const tbody = document.querySelector('#tabla-inscripciones tbody');
    tbody.innerHTML = '';

    if (inscripciones.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="loading">No hay inscripciones registradas</td></tr>';
        return;
    }

    inscripciones.forEach(insc => {
        const fechaGira = new Date(insc.FechaGira).toLocaleDateString('es-PA');
        const fechaInsc = new Date(insc.FechaInscripcion).toLocaleString('es-PA');
        const estadoBadge = obtenerBadgeEstado(insc.Estado);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${insc.InscripcionID}</td>
            <td>${insc.NombreUsuario}</td>
            <td>${insc.Correo}</td>
            <td>${insc.NombreGira}</td>
            <td>${fechaGira}</td>
            <td>${insc.Lugar}</td>
            <td>${estadoBadge}</td>
            <td>${fechaInsc}</td>
        `;
        tbody.appendChild(tr);
    });
}

// ---------- UTILIDADES ----------
function obtenerBadgeRol(rol) {
    const badges = {
        'Administrador': '<span class="badge badge-admin">Administrador</span>',
        'Organizador': '<span class="badge badge-organizador">Organizador</span>',
        'Estudiante': '<span class="badge badge-estudiante">Estudiante</span>'
    };
    return badges[rol] || rol;
}

function obtenerBadgeEstado(estado) {
    const badges = {
        'Registrado': '<span class="badge badge-registrado">Registrado</span>',
        'Confirmado': '<span class="badge badge-confirmado">Confirmado</span>',
        'Cancelado': '<span class="badge badge-cancelado">Cancelado</span>'
    };
    return badges[estado] || estado;
}

function truncarTexto(texto, maxLength) {
    if (texto.length <= maxLength) return texto;
    return texto.substring(0, maxLength) + '...';
}