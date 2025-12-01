<?php
// controllers/ComprobanteController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Comprobante.php';

class ComprobanteController {
    private $db;
    private $comprobante;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->comprobante = new Comprobante($this->db);
    }

    public function mostrarFormularioIngreso() {
        include __DIR__ . '/../pages/ingreso-estudiante.php';
    }

    public function subirComprobante() {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validar campos requeridos
                $nombre = $_POST['nombre'] ?? '';
                $apellido = $_POST['apellido'] ?? '';
                $email = $_POST['email'] ?? '';
                $cedula = $_POST['cedula'] ?? '';
                $numeroComprobante = $_POST['numeroComprobante'] ?? '';

                if (empty($nombre) || empty($apellido) || empty($email) || 
                    empty($cedula) || empty($numeroComprobante)) {
                    throw new Exception("Todos los campos son obligatorios");
                }

                // Validar archivo
                if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) {
                    throw new Exception("Debe seleccionar un archivo de comprobante");
                }

                if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("Error al subir el archivo");
                }

                // Validar tipo de archivo
                $tipoArchivo = $_FILES['archivo']['type'];
                $extensionesPermitidas = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                
                if (!in_array($tipoArchivo, $extensionesPermitidas)) {
                    throw new Exception("Solo se permiten archivos de imagen (JPG, PNG) o PDF");
                }

                // Validar tamaño (5MB max)
                $maxSize = 5 * 1024 * 1024; // 5MB
                if ($_FILES['archivo']['size'] > $maxSize) {
                    throw new Exception("El archivo no debe superar los 5MB");
                }

                // Verificar si ya existe el email o cédula
                if ($this->comprobante->existeEmail($email)) {
                    throw new Exception("Ya existe un comprobante registrado con este correo");
                }

                if ($this->comprobante->existeCedula($cedula)) {
                    throw new Exception("Ya existe un comprobante registrado con esta cédula");
                }

                // Leer archivo
                $archivoContenido = file_get_contents($_FILES['archivo']['tmp_name']);

                // Guardar en base de datos
                $this->comprobante->nombreEstudiante = $nombre;
                $this->comprobante->apellidoEstudiante = $apellido;
                $this->comprobante->emailEstudiante = $email;
                $this->comprobante->cedula = $cedula;
                $this->comprobante->numeroComprobante = $numeroComprobante;
                $this->comprobante->archivoComprobante = $archivoContenido;
                $this->comprobante->nombreArchivo = $_FILES['archivo']['name'];
                $this->comprobante->tipoArchivo = $tipoArchivo;
                $this->comprobante->estado = Comprobante::ESTADO_PENDIENTE;

                if ($this->comprobante->guardar()) {
                    echo "<h1>¡Comprobante registrado exitosamente!</h1>";
                    echo "<p>Tu solicitud está en revisión.</p>";
                    echo "<p><a href='/giras_tecnicas/pages/user-dashboard.html'>Volver al dashboard</a></p>";
                } else {
                    throw new Exception("Error al guardar el comprobante");
                }

            } catch (Exception $e) {
                $error = $e->getMessage();
                include __DIR__ . '/../pages/ingreso-estudiante.php';
            }
        }
    }

    public function mostrarVerificacion() {
        include __DIR__ . '/../pages/verificar-estado.php';
    }

    public function verificarEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            
            $comprobante = $this->comprobante->obtenerPorEmail($email);
            
            if ($comprobante) {
                if ($comprobante['estado'] === Comprobante::ESTADO_APROBADO) {
                    $mensaje = "¡Felicitaciones! Estás registrado para la gira.";
                    $tipoMensaje = "exito";
                } elseif ($comprobante['estado'] === Comprobante::ESTADO_RECHAZADO) {
                    $mensaje = "Tu comprobante fue rechazado. Motivo: " . $comprobante['motivoRechazo'];
                    $tipoMensaje = "rechazo";
                } else {
                    $mensaje = "Tu comprobante está en revisión. Por favor espera.";
                    $tipoMensaje = "pendiente";
                }
            } else {
                $mensaje = "No se encontró ningún registro con este correo.";
                $tipoMensaje = "error";
            }
            
            echo "<h1>Estado del Comprobante</h1>";
            echo "<p>" . $mensaje . "</p>";
        }
    }

    public function listarComprobantes() {
        $comprobantes = $this->comprobante->obtenerTodos();
        include __DIR__ . '/../pages/admin-comprobantes.php';
    }

    public function descargarComprobante($id) {
        $comprobante = $this->comprobante->obtenerPorId($id);
        
        if ($comprobante) {
            header('Content-Type: ' . $comprobante['tipoArchivo']);
            header('Content-Disposition: attachment; filename="' . $comprobante['nombreArchivo'] . '"');
            echo $comprobante['archivoComprobante'];
            exit;
        } else {
            http_response_code(404);
            echo "Archivo no encontrado";
        }
    }

    public function aprobarComprobante($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->comprobante->aprobar($id);
            header('Location: /giras_tecnicas/pages/admin.php');
            exit;
        }
    }

    public function rechazarComprobante($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motivo = $_POST['motivo'] ?? 'No especificado';
            $this->comprobante->rechazar($id, $motivo);
            header('Location: /giras_tecnicas/pages/admin.php');
            exit;
        }
    }
}
?>