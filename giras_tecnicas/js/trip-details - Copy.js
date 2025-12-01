// ---- OBTENER PARÁMETROS DE URL ----
function getQueryParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

// ---- CARGAR DETALLES DE LA GIRA ----
document.addEventListener('DOMContentLoaded', async () => {
    const giraId = getQueryParam('id');
    if (!giraId) {
        alert('No se ha especificado una gira válida');
        history.back();
        return;
    }

    try {
        const response = await fetch(`../api/giras.php?id=${giraId}`);
        const result = await response.json();

        console.log('Respuesta de la API:', result); // DEBUG

        if (!result.success || !result.data) {
            alert('No se pudo cargar la información de la gira');
            history.back();
            return;
        }

        const gira = result.data;

        // Llenar datos principales
        document.getElementById('gira-nombre').textContent = gira.Nombre;
        document.getElementById('gira-lugar').textContent = gira.Lugar;

        const fecha = new Date(gira.Fecha).toLocaleDateString('es-PA', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('gira-fecha').textContent = fecha;

        // Descripción / resumen
        const descripcion = gira.Descripcion || 'Sin descripción disponible';
        document.getElementById('gira-descripcion').textContent = descripcion;
        document.getElementById('gira-resumen').textContent = descripcion;

        // Inscritos
        document.getElementById('gira-cupos').textContent =
            `${gira.totalInscritos || 0} inscritos`;

        // Extraer MONTO, TRANSPORTE y HORA de la descripción
        // Formato esperado en la descripción:
        // [MONTO=15] [TRANSPORTE=SI/NO] [HORA=8:00 AM]
        let monto = 0.00;
        let transporte = 'No especificado';
        let hora = 'Por definir';

        const montoMatch = descripcion.match(/\[MONTO=([0-9]+(\.\[0-9]{1,2})?)\]/i);
        if (montoMatch) {
            monto = parseFloat(montoMatch[1]);
            console.log('Monto extraído:', monto); // DEBUG
        }

        const transporteMatch = descripcion.match(/\[TRANSPORTE=(SI|NO)\]/i);
        if (transporteMatch) {
            transporte = transporteMatch[1].toUpperCase() === 'SI'
                ? 'Incluye transporte'
                : 'No incluye transporte';
            console.log('Transporte extraído:', transporte); // DEBUG
        }

        const horaMatch = descripcion.match(/\[HORA=([^\]]+)\]/i);
        if (horaMatch) {
            hora = horaMatch[1].trim();
            console.log('Hora extraída:', hora); // DEBUG
        }

        document.getElementById('gira-monto').textContent = `B/. ${monto.toFixed(2)}`;
        document.getElementById('gira-transporte').textContent = transporte;
        document.getElementById('gira-hora').textContent = hora;

        document.getElementById('monto-modal').textContent = `B/. ${monto.toFixed(2)}`;
        document.getElementById('confirm-monto').textContent = `B/. ${monto.toFixed(2)}`;

        // Guardar datos en window para usarlos en el modal de confirmación
        window.giraActual = {
            id: gira.GiraID,
            nombre: gira.Nombre,
            monto,
        };

        // Cargar nombre del estudiante desde la sesión
        try {
            const sessionResponse = await fetch('../api/session.php');
            const sessionData = await sessionResponse.json();
            if (sessionData.success && sessionData.data && sessionData.data.usuario) {
                window.estudianteActual = sessionData.data.usuario;
            }
        } catch (e) {
            console.warn('No se pudo obtener el nombre del estudiante para el resumen');
        }

        inicializarModales();

    } catch (error) {
        console.error('Error al cargar detalles de la gira:', error);
        alert('Error al cargar los detalles de la gira');
        history.back();
    }
});

// ---- MODALES ----
function inicializarModales() {
    const modalPago = document.getElementById('modal-pago');
    const modalConfirm = document.getElementById('modal-confirm');
    const btnRegistrar = document.getElementById('btn-registrar');

    const formPago = modalPago.querySelector('form');
    const btnCancelarPago = modalPago.querySelector("button[type='button']");
    const btnCancelarConfirm = document.getElementById('btn-cancelar-confirm');
    const btnConfirmar = document.getElementById('btn-confirmar');

    // ABRIR PRIMER MODAL
    btnRegistrar.addEventListener('click', () => {
        modalPago.showModal();
    });

    // CANCELAR PRIMER MODAL
    btnCancelarPago.addEventListener('click', () => {
        modalPago.close();
    });

    // CONTINUAR → ABRE MODAL CONFIRMACIÓN
    formPago.addEventListener('submit', (e) => {
        e.preventDefault();

        const metodo = formPago.pago.value;
        const textoMetodo = {
            efectivo: 'Efectivo',
            tarjeta: 'Tarjeta Crédito/Débito',
            transferencia: 'Transferencia Bancaria',
            yappy: 'Yappy',
        }[metodo] || metodo;

        // Llenar resumen en modal de confirmación
        document.getElementById('confirm-gira').textContent =
            window.giraActual?.nombre || '';
        document.getElementById('confirm-estudiante').textContent =
            window.estudianteActual?.nombre || '';
        document.getElementById('confirm-pago').textContent = textoMetodo;

        modalPago.close();
        modalConfirm.showModal();
    });

    // CANCELAR CONFIRMACIÓN
    btnCancelarConfirm.addEventListener('click', () => {
        modalConfirm.close();
    });

    // CONFIRMAR REGISTRO (por ahora solo mensaje)
    btnConfirmar.addEventListener('click', () => {
        modalConfirm.close();
        alert('Registro enviado al coordinador ✔');
    });
}
