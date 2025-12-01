<?php
require_once __DIR__ . '/../config/conexion.php';

class Gira {
    private $conn;
    
    public function __construct() {
        $this->conn = conectarDB();
    }
    
    // Insertar nueva gira
    public function insertar($nombre, $fecha, $lugar, $descripcion, $organizadorID) {
        $sql = "INSERT INTO Giras (Nombre, Fecha, Lugar, Descripcion, OrganizadorID) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $fecha, $lugar, $descripcion, $organizadorID);
        
        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            $stmt->close();
            return $id;
        }
        $stmt->close();
        return false;
    }
    
    // Actualizar gira
    public function actualizar($giraID, $nombre, $fecha, $lugar, $descripcion, $organizadorID) {
        $sql = "UPDATE Giras SET Nombre = ?, Fecha = ?, Lugar = ?, Descripcion = ?, OrganizadorID = ? WHERE GiraID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssii", $nombre, $fecha, $lugar, $descripcion, $organizadorID, $giraID);
        
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    // Eliminar gira
    public function eliminar($giraID) {
        $sql = "DELETE FROM Giras WHERE GiraID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $giraID);
        
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    // Listar todas las giras
    public function listar() {
        $sql = "SELECT * FROM Giras ORDER BY Fecha ASC";
        $resultado = $this->conn->query($sql);
        
        $giras = [];
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $giras[] = $fila;
            }
        }
        return $giras;
    }
    
    // Listar giras disponibles (futuras)
    public function listarDisponibles() {
        $sql = "SELECT * FROM Giras WHERE Fecha >= CURDATE() ORDER BY Fecha ASC";
        $resultado = $this->conn->query($sql);
        
        $giras = [];
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $giras[] = $fila;
            }
        }
        return $giras;
    }
    
    // Buscar gira por ID
    public function buscarPorID($giraID) {
        $sql = "SELECT * FROM Giras WHERE GiraID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $giraID);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $gira = $resultado->fetch_assoc();
        $stmt->close();
        
        return $gira;
    }
    
    public function __destruct() {
        cerrarDB($this->conn);
    }
}
?>