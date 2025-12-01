<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso de Comprobante - Giras UTP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-4">
    <div class="max-w-2xl mx-auto mt-8">
        <!-- Header -->
        <div class="bg-blue-800 text-white p-6 rounded-t-xl shadow-lg">
            <h1 class="text-3xl font-bold text-center">Giras UTP Panamá</h1>
            <p class="text-center mt-2 text-blue-100">Sistema de Registro de Comprobantes</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white p-8 rounded-b-xl shadow-lg">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                Ingreso de Comprobante de Pago
            </h2>

            <!-- Mensaje de error -->
            <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p class="font-semibold">Error:</p>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>

            <form action="../controllers/ComprobanteController.php" method="post" enctype="multipart/form-data" class="space-y-5">
                
                <!-- Nombre -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nombre *</label>
                    <input type="text" name="nombre" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>

                <!-- Apellido -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Apellido *</label>
                    <input type="text" name="apellido" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Correo Electrónico *</label>
                    <input type="email" name="email" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="ejemplo@utp.ac.pa" required>
                </div>

                <!-- Cédula -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Cédula *</label>
                    <input type="text" name="cedula" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="8-123-4567" required>
                </div>

                <!-- Número de Comprobante -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Número de Comprobante *</label>
                    <input type="text" name="numeroComprobante" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Ej: 123456789" required>
                </div>

                <!-- Archivo -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Comprobante de Pago (Imagen o PDF) *</label>
                    <input type="file" name="archivo" accept="image/*,application/pdf" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                    <p class="text-sm text-gray-500 mt-2">Formatos permitidos: JPG, PNG, PDF (Máx. 5MB)</p>
                </div>

                <!-- Botones -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-800 transition duration-300 shadow-md">
                        Enviar Comprobante
                    </button>
                    <a href="user-dashboard.html" class="flex-1 bg-gray-500 text-white font-semibold py-3 px-6 rounded-lg hover:bg-gray-600 transition duration-300 shadow-md text-center leading-[3rem]">
                        Cancelar
                    </a>
                </div>
            </form>

            <!-- Nota informativa -->
            <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-sm text-gray-700">
                    <strong>Nota:</strong> Una vez enviado tu comprobante, será revisado por el personal administrativo. 
                    Recibirás una confirmación por correo electrónico.
                </p>
            </div>
        </div>
    </div>
</body>
</html>