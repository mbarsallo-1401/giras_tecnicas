<?php
require_once __DIR__ . '/../config/conexion.php';

class Inscripcion {
    private $conn;
    
    public function __construct() {
        $this->conn = conectarDB();
    }
    
    // Registrar inscripción
    public function insertar($usuarioID, $giraID, $estado = 'Registrado') {
        // Verificar si ya existe inscripción
        if ($this->existeInscripcion($usuarioID, $giraID)) {
            return false;
        }
        
        $sql = "INSERT INTO Inscripciones (UsuarioID, GiraID, Estado) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $usuarioID, $giraID, $estado);
        
        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            $stmt->close();
            return $id;
        }
        $stmt->close();
        return false;
    }
    
    // Actualizar estado de inscripción
    public function actualizarEstado($inscripcionID, $nuevoEstado) {
        $sql = "UPDATE Inscripciones SET Estado = ? WHERE InscripcionID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $nuevoEstado, $inscripcionID);
        
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    // Cancelar inscripción (eliminar)
    public function eliminar($inscripcionID) {
        $sql = "DELETE FROM Inscripciones WHERE InscripcionID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $inscripcionID);
        
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    // Listar inscripciones de un usuario
    public function listarPorUsuario($usuarioID) {
        $sql = "SELECT I.*, G.Nombre as NombreGira, G.Fecha, G.Lugar 
                FROM Inscripciones I 
                INNER JOIN Giras G ON I.GiraID = G.GiraID 
                WHERE I.UsuarioID = ? 
                ORDER BY I.FechaInscripcion DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuarioID);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $inscripciones = [];
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $inscripciones[] = $fila;
            }
        }
        $stmt->close();
        
        return $inscripciones;
    }
    
    // Listar inscripciones de una gira
    public function listarPorGira($giraID) {
        $sql = "SELECT I.*, U.Nombre, U.Correo 
                FROM Inscripciones I 
                INNER JOIN Usuarios U ON I.UsuarioID = U.UsuarioID 
                WHERE I.GiraID = ? 
                ORDER BY I.FechaInscripcion DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $giraID);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $inscripciones = [];
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $inscripciones[] = $fila;
            }
        }
        $stmt->close();
        
        return $inscripciones;
    }
    
    // Verificar si existe una inscripción
    private function existeInscripcion($usuarioID, $giraID) {
        $sql = "SELECT InscripcionID FROM Inscripciones WHERE UsuarioID = ? AND GiraID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $usuarioID, $giraID);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $existe = $resultado->num_rows > 0;
        $stmt->close();
        
        return $existe;
    }
    
    // Contar inscripciones por gira
    public function contarPorGira($giraID) {
        $sql = "SELECT COUNT(*) as total FROM Inscripciones WHERE GiraID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $giraID);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();
        
        return $fila['total'];
    }
    
    public function __destruct() {
        cerrarDB($this->conn);
    }
}
?>