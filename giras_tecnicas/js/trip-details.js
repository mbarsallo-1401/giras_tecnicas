function getQueryParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

// Formatear fecha "YYYY-MM-DD" → "12 de noviembre de 2025"
function formatFecha(fechaStr) {
    if (!fechaStr) return '';

    const partes = fechaStr.split('-'); // [YYYY, MM, DD]
    if (partes.length !== 3) return fechaStr;

    const year = partes[0];
    const month = parseInt(partes[1], 10);
    const day = parseInt(partes[2], 10);

    const meses = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
    ];

    const nombreMes = meses[month - 1] || '';
    return `${day} de ${nombreMes} de ${year}`;
}

document.addEventListener('DOMContentLoaded', async () => {
    const giraId = getQueryParam('id');

    if (!giraId) {
        alert('No se ha especificado una gira válida');
        return;
    }

    try {
        // 1. LLAMAR A LA API
        const response = await fetch(`../api/giras.php?id=${giraId}`);
        const result = await response.json();

        if (!result.success || !result.data) {
            alert('No se pudo cargar la información de la gira');
            return;
        }

        const gira = result.data;

        // 2. PINTAR DATOS REALES
        const nombre = document.getElementById('gira-nombre');
        const lugar = document.getElementById('gira-lugar');
        const fecha = document.getElementById('gira-fecha');
        const descripcion = document.getElementById('gira-descripcion');
        const resumen = document.getElementById('gira-resumen');
        const cupos = document.getElementById('gira-cupos');
        const monto = document.getElementById('gira-monto');
        const transporte = document.getElementById('gira-transporte');
        const hora = document.getElementById('gira-hora');

        if (nombre) nombre.textContent = gira.Nombre;
        if (lugar) lugar.textContent = gira.Lugar;

        // Formatear la fecha
        const fechaFormateada = formatFecha(gira.Fecha);
        if (fecha) fecha.textContent = fechaFormateada || gira.Fecha;

        const textoDescripcion = gira.Descripcion || 'Sin descripción disponible';
        if (descripcion) descripcion.textContent = textoDescripcion;
        if (resumen) resumen.textContent = textoDescripcion;
        if (cupos) cupos.textContent = (gira.totalInscritos || 0) + ' inscritos';

        // 3. EXTRAER MONTO, TRANSPORTE Y HORA DE LA DESCRIPCIÓN
        let montoVal = 0.00;
        let transporteVal = 'No especificado';
        let horaVal = 'Por definir';

        const texto = gira.Descripcion || '';

        const montoMatch = texto.match(/\[MONTO=([0-9]+(\.[0-9]{1,2})?)\]/i);
        if (montoMatch) {
            montoVal = parseFloat(montoMatch[1]);
        }

        const transporteMatch = texto.match(/\[TRANSPORTE=(SI|NO)\]/i);
        if (transporteMatch) {
            transporteVal = transporteMatch[1].toUpperCase() === 'SI'
                ? 'Incluye transporte'
                : 'No incluye transporte';
        }

        const horaMatch = texto.match(/\[HORA=([^\]]+)\]/i);
        if (horaMatch) {
            horaVal = horaMatch[1].trim();
        }

        if (monto) monto.textContent = `B/. ${montoVal.toFixed(2)}`;
        if (transporte) transporte.textContent = transporteVal;
        if (hora) hora.textContent = horaVal;

        // 4. ACTUALIZAR MONTOS EN LOS MODALES (si existen)
        const montoModal = document.getElementById('monto-modal');
        const confirmMonto = document.getElementById('confirm-monto');

        if (montoModal) montoModal.textContent = `B/. ${montoVal.toFixed(2)}`;
        if (confirmMonto) confirmMonto.textContent = `B/. ${montoVal.toFixed(2)}`;

    } catch (error) {
        console.error('Error al cargar detalles de la gira:', error);
        alert('Error al cargar los detalles de la gira');
    }
});