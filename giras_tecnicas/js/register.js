// =============================
// VALIDACIÓN DE REGISTRO UTP
// =============================

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#register-form");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Obtener campos
        const nombre = document.querySelector("#nombre");
        const apellido = document.querySelector("#apellido");
        const email = document.querySelector("#email");
        const password = document.querySelector("#password");
        const confirmPassword = document.querySelector("#confirmPassword");

        // Validación básica
        if (nombre.value.trim() === "") {
            alert("El nombre es obligatorio");
            return;
        }

        if (apellido.value.trim() === "") {
            alert("El apellido es obligatorio");
            return;
        }

        if (!email.value.includes("@")) {
            alert("El correo electrónico no es válido");
            return;
        }

        if (password.value.length < 6) {
            alert("La contraseña debe tener mínimo 6 caracteres");
            return;
        }

        if (confirmPassword && password.value !== confirmPassword.value) {
            alert("Las contraseñas no coinciden");
            return;
        }

        // Deshabilitar botón durante la petición
        const submitBtn = form.querySelector('button[type="submit"]');
        const btnText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = "Registrando...";

        try {
            const response = await fetch('../api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    nombre: nombre.value.trim() + ' ' + apellido.value.trim(),
                    email: email.value.trim(),
                    password: password.value,
                    confirmPassword: confirmPassword ? confirmPassword.value : password.value
                })
            });

            const result = await response.json();

            if (result.success) {
                alert("¡Registro exitoso! Bienvenido/a");
                window.location.href = "user-dashboard.html";
            } else {
                alert(result.message || 'Error al registrar');
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