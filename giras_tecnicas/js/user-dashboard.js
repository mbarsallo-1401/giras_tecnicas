// ---- VERIFICAR SESIÓN ----
document.addEventListener("DOMContentLoaded", async () => {
    try {
        const sessionResponse = await fetch('../api/session.php');
        const sessionData = await sessionResponse.json();

        console.log('sessionData:', sessionData); // <- para depurar en consola

        if (!sessionData.success) {
            window.location.href = 'login.html';
            return;
        }

        const userNameElement = document.querySelector('.user-name');
        if (userNameElement && sessionData.data && sessionData.data.usuario) {
            const nombre = sessionData.data.usuario.nombre || '';
            userNameElement.textContent = nombre
                ? `Bienvenido, ${nombre}`
                : 'Bienvenido';
        }
    } catch (error) {
        console.error('Error al verificar sesión:', error);
        window.location.href = 'login.html';
        return;
    }

    await cargarGiras();

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

// ---- CARGAR GIRAS DESDE LA API ----
async function cargarGiras() {
    try {
        const response = await fetch('../api/giras.php?disponibles=true');
        const result = await response.json();

        if (result.success && result.data) {
            const girasContainer = document.querySelector('.grid');
            if (!girasContainer) {
                console.log('No se encontró el contenedor .grid');
                return;
            }

            girasContainer.innerHTML = '';

            result.data.forEach(gira => {
                const giraCard = crearTarjetaGira(gira);
                girasContainer.appendChild(giraCard);
            });
        }
    } catch (error) {
        console.error('Error al cargar giras:', error);
    }
}

// ---- CREAR TARJETA DE GIRA ----
function crearTarjetaGira(gira) {
    const card = document.createElement('div');
    card.className = 'card';

    const fecha = new Date(gira.Fecha).toLocaleDateString('es-PA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Extraer monto y transporte de la descripción
    const descripcion = gira.Descripcion || '';
    let monto = '0.00';
    let transporte = '';

    const montoMatch = descripcion.match(/\[MONTO=([0-9]+(\.\[0-9]{1,2})?)\]/i);
    if (montoMatch) {
        monto = parseFloat(montoMatch[1]).toFixed(2);
    }

    const transporteMatch = descripcion.match(/\[TRANSPORTE=(SI|NO)\]/i);
    if (transporteMatch) {
        transporte = transporteMatch[1].toUpperCase() === 'SI'
            ? 'Incluye transporte'
            : 'No incluye transporte';
    }

    card.innerHTML = `
        <div class="card-image">
            <img src="../img/logo.png" alt="${gira.Nombre}">
        </div>
        <div class="card-info">
            <h3>${gira.Nombre}</h3>
            <span class="badge badge-green">Disponible</span>
            <p class="subtitle">${fecha}</p>
            <p class="subtitle">${gira.Lugar}</p>
            <p class="subtitle">${gira.totalInscritos || 0} inscritos</p>
            <p class="subtitle">Monto: B/. ${monto} · ${transporte}</p>
            <p class="description">
                ${gira.Descripcion || 'Sin descripción disponible'}
            </p>
            <div class="card-actions">
                <button class="btn-primary" onclick="verDetalles(${gira.GiraID})">Ver Detalles</button>
                <button class="btn-secondary" onclick="inscribirse(${gira.GiraID})">Inscribirse</button>
            </div>
        </div>
    `;

    return card;
}

// ---- VER DETALLES DE GIRA ----
function verDetalles(giraId) {
    window.location.href = `trip-details.html?id=${giraId}`;
}

// ---- INSCRIBIRSE EN GIRA ----
async function inscribirse(giraId) {
    if (!confirm('¿Desea inscribirse en esta gira?')) return;

    try {
        const response = await fetch('../api/inscripciones.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                gira_id: giraId
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('¡Inscripción exitosa!');
            await cargarGiras();
        } else {
            alert(result.message || 'Error al inscribirse');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión. Por favor intente nuevamente.');
    }
}
