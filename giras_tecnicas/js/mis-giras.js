// ---------- VERIFICAR SESIÓN ----------
document.addEventListener("DOMContentLoaded", async () => {
    // Verificar si hay sesión activa
    try {
        const sessionResponse = await fetch('../api/session.php');
        const sessionData = await sessionResponse.json();

        if (!sessionData.success) {
            window.location.href = 'login.html';
            return;
        }

        // Mostrar nombre del usuario
        const userNameElement = document.querySelector('.user-name');
        if (userNameElement && sessionData.data.usuario) {
            userNameElement.textContent = sessionData.data.usuario.nombre;
        }

        // Guardar ID del usuario para usarlo después
        window.currentUserId = sessionData.data.usuario.id;

        // Cargar mis giras
        await cargarMisGiras();

    } catch (error) {
        console.error('Error al verificar sesión:', error);
        window.location.href = 'login.html';
        return;
    }

    // Configurar botón de cerrar sesión
    const btnLogout = document.querySelector(".btn-outline");
    if (btnLogout) {
        btnLogout.addEventListener("click", async () => {
            try {
                await fetch('../api/logout.php');
                window.location.href = "login.html";
            } catch (error) {
                console.error('Error al cerrar sesión:', error);
                window.location.href = "login.html";
            }
        });
    }
});

// ---------- CARGAR MIS GIRAS ----------
async function cargarMisGiras() {
    const loadingSpinner = document.getElementById('loading-spinner');
    const girasGrid = document.getElementById('giras-grid');
    const noGirasMessage = document.getElementById('no-giras-message');

    try {
        // Mostrar spinner
        loadingSpinner.style.display = 'flex';
        girasGrid.style.display = 'none';
        noGirasMessage.style.display = 'none';

        // Obtener inscripciones del usuario actual
        const response = await fetch(`../api/inscripciones.php?usuario_id=${window.currentUserId}`);
        const result = await response.json();

        // Ocultar spinner
        loadingSpinner.style.display = 'none';

        if (result.success && result.data && result.data.length > 0) {
            girasGrid.style.display = 'grid';
            girasGrid.innerHTML = '';

            result.data.forEach(inscripcion => {
                const giraCard = crearTarjetaGira(inscripcion);
                girasGrid.appendChild(giraCard);
            });
        } else {
            // No hay giras
            noGirasMessage.style.display = 'flex';
        }
    } catch (error) {
        console.error('Error al cargar mis giras:', error);
        loadingSpinner.style.display = 'none';
        noGirasMessage.style.display = 'flex';
    }
}

// ---------- CREAR TARJETA DE GIRA ----------
function crearTarjetaGira(inscripcion) {
    const card = document.createElement('div');
    card.className = 'card';

    const fecha = new Date(inscripcion.Fecha).toLocaleDateString('es-PA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    const fechaInscripcion = new Date(inscripcion.FechaInscripcion).toLocaleDateString('es-PA', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });

    // Determinar badge según estado
    let badgeClass = 'badge-green';
    let badgeText = inscripcion.Estado;

    if (inscripcion.Estado === 'Cancelado') {
        badgeClass = 'badge-red';
    } else if (inscripcion.Estado === 'Confirmado') {
        badgeClass = 'badge-blue';
    }

    card.innerHTML = `
        <div class="card-image">
          <img src="../img/logo.png" alt="${inscripcion.NombreGira}">
        </div>
        <div class="card-info">
          <h3>${inscripcion.NombreGira}</h3>
          <span class="badge ${badgeClass}">${badgeText}</span>
          <p class="subtitle"><strong>📅 Fecha de la gira:</strong> ${fecha}</p>
          <p class="subtitle"><strong>📍 Lugar:</strong> ${inscripcion.Lugar}</p>
          <p class="subtitle"><strong>✅ Inscrito el:</strong> ${fechaInscripcion}</p>
          <div class="card-actions">
            <button class="btn-primary" onclick="verDetalles(${inscripcion.GiraID})">Ver Detalles</button>
            <button class="btn-danger" onclick="cancelarInscripcion(${inscripcion.InscripcionID}, '${inscripcion.NombreGira}')">Cancelar</button>
          </div>
        </div>
    `;

    return card;
}

// ---------- VER DETALLES DE GIRA ----------
function verDetalles(giraId) {
    window.location.href = `trip-details.html?id=${giraId}`;
}

// ---------- CANCELAR INSCRIPCIÓN ----------
async function cancelarInscripcion(inscripcionId, nombreGira) {
    if (!confirm(`¿Estás seguro de que deseas cancelar tu inscripción en "${nombreGira}"?`)) {
        return;
    }

    try {
        const response = await fetch('../api/inscripciones.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: inscripcionId
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Inscripción cancelada exitosamente');
            await cargarMisGiras(); // Recargar giras
        } else {
            alert(result.message || 'Error al cancelar la inscripción');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión. Por favor intente nuevamente.');
    }
}