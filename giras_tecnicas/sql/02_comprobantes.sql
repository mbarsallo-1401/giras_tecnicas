-- 02_comprobantes.sql
-- Tabla para comprobantes de pago

USE GirasTecnicas;

-- Tabla Comprobantes (para subir pagos)
CREATE TABLE IF NOT EXISTS comprobantes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nombreEstudiante VARCHAR(100) NOT NULL,
    apellidoEstudiante VARCHAR(100) NOT NULL,
    emailEstudiante VARCHAR(150) NOT NULL UNIQUE,
    cedula VARCHAR(50) NOT NULL UNIQUE,
    numeroComprobante VARCHAR(100) NOT NULL,
    archivoComprobante LONGBLOB NOT NULL,
    nombreArchivo VARCHAR(255) NOT NULL,
    tipoArchivo VARCHAR(100) NOT NULL,
    estado ENUM('PENDIENTE', 'APROBADO', 'RECHAZADO') DEFAULT 'PENDIENTE',
    fechaSubida DATETIME DEFAULT CURRENT_TIMESTAMP,
    fechaAprobacion DATETIME NULL,
    motivoRechazo TEXT NULL,
    INDEX idx_email (emailEstudiante),
    INDEX idx_cedula (cedula),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;