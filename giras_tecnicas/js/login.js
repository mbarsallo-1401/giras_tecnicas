document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("login-form");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        clearErrors();
        let isValid = true;

        // Validar email
        if (emailInput.value.trim() === "") {
            showError(emailInput, "El correo es obligatorio");
            isValid = false;
        }

        // Validar contraseña
        if (passwordInput.value.trim() === "") {
            showError(passwordInput, "La contraseña es obligatoria");
            isValid = false;
        }

        if (!isValid) return;

        // Deshabilitar botón durante la petición
        const submitBtn = form.querySelector('button[type="submit"]');
        const btnText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = "Iniciando sesión...";

        try {
            const response = await fetch('../api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: emailInput.value.trim(),
                    password: passwordInput.value
                })
            });

            const result = await response.json();

            if (result.success) {
                // Redirigir según el rol del usuario
                const rol = result.data.usuario.rol;
                
                if (rol === 'Estudiante') {
                    window.location.href = 'user-dashboard.html';
                } else if (rol === 'Organizador') {
                    window.location.href = 'coordinador-dashboard.html';
                } else if (rol === 'Administrador') {
                    window.location.href = 'admin.php';
                } else {
                    window.location.href = 'user-dashboard.html';
                }
            } else {
                alert(result.message || 'Error al iniciar sesión');
                submitBtn.disabled = false;
                submitBtn.textContent = btnText;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión. Por favor intente nuevamente.');
            submitBtn.disabled = false;
            submitBtn.textContent = btnText;
        }
    });
});

// ---- Funciones auxiliares ----

function showError(input, message) {
    const container = input.parentElement;
    const errorText = container.querySelector("small");
    errorText.textContent = message;
    errorText.style.display = "block";
}

function clearErrors() {
    const errors = document.querySelectorAll(".form-control small");
    errors.forEach(error => {
        error.textContent = "";
        error.style.display = "none";
    });
}