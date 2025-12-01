// =============================
// COORDINADOR DASHBOARD LOGIC
// =============================

// Confirmación de cierre de sesión
document.querySelector(".logout-btn")?.addEventListener("click", () => {
    if (confirm("¿Desea cerrar sesión?")) {
        window.location.href = "login.html";
    }
});

// Eliminar tarjetas de giras
document.querySelectorAll(".delete-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
        if (confirm("¿Eliminar esta gira?")) {
            btn.parentElement.remove();
        }
    });
});

// Aprobar estudiantes
document.querySelectorAll(".approve").forEach((btn) => {
    btn.addEventListener("click", () => {
        alert("Estudiante aprobado ✔");
    });
});

// Rechazar estudiantes
document.querySelectorAll(".reject").forEach((btn) => {
    btn.addEventListener("click", () => {
        alert("Estudiante rechazado ✖");
    });
});