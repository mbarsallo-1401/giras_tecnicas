<?php
require_once __DIR__ . '/../config/database.php';

class Comprobante {
    private $conn;
    private $table_name = "comprobantes";

    public $id;
    public $nombreEstudiante;
    public $apellidoEstudiante;
    public $emailEstudiante;
    public $cedula;
    public $numeroComprobante;
    public $archivoComprobante;
    public $nombreArchivo;
    public $tipoArchivo;
    public $estado;
    public $fechaSubida;
    public $fechaAprobacion;
    public $motivoRechazo;

    const ESTADO_PENDIENTE = 'PENDIENTE';
    const ESTADO_APROBADO = 'APROBADO';
    const ESTADO_RECHAZADO = 'RECHAZADO';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function guardar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nombreEstudiante=:nombre, 
                      apellidoEstudiante=:apellido,
                      emailEstudiante=:email,
                      cedula=:cedula,
                      numeroComprobante=:numeroComprobante,
                      archivoComprobante=:archivo,
                      nombreArchivo=:nombreArchivo,
                      tipoArchivo=:tipoArchivo,
                      estado=:estado,
                      fechaSubida=NOW()";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nombre", $this->nombreEstudiante);
        $stmt->bindParam(":apellido", $this->apellidoEstudiante);
        $stmt->bindParam(":email", $this->emailEstudiante);
        $stmt->bindParam(":cedula", $this->cedula);
        $stmt->bindParam(":numeroComprobante", $this->numeroComprobante);
        $stmt->bindParam(":archivo", $this->archivoComprobante);
        $stmt->bindParam(":nombreArchivo", $this->nombreArchivo);
        $stmt->bindParam(":tipoArchivo", $this->tipoArchivo);
        $stmt->bindParam(":estado", $this->estado);

        return $stmt->execute();
    }

    public function existeEmail($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE emailEstudiante = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function existeCedula($cedula) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE cedula = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $cedula);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function obtenerPorEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE emailEstudiante = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fechaSubida DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function aprobar($id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = :estado, fechaAprobacion = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $estado = self::ESTADO_APROBADO;
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    public function rechazar($id, $motivo) {
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = :estado, motivoRechazo = :motivo 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $estado = self::ESTADO_RECHAZADO;
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":motivo", $motivo);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }
}
?>