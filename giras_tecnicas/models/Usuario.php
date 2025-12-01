<?php
require_once __DIR__ . '/../config/conexion.php';

class Usuario {
    private $conn;
    
    public function __construct() {
        $this->conn = conectarDB();
    }
    
    // Insertar nuevo usuario
    public function insertar($nombre, $correo, $contrasenaHash, $rol) {
        $sql = "INSERT INTO Usuarios (Nombre, Correo, ContrasenaHash, Rol) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $correo, $contrasenaHash, $rol);
        
        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            $stmt->close();
            return $id;
        }
        $stmt->close();
        return false;
    }
    
    // Actualizar usuario
    public function actualizar($usuarioID, $nombre, $correo, $contrasenaHash, $rol) {
        $sql = "UPDATE Usuarios SET Nombre = ?, Correo = ?, ContrasenaHash = ?, Rol = ? WHERE UsuarioID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $correo, $contrasenaHash, $rol, $usuarioID);
        
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    // Eliminar usuario
    public function eliminar($usuarioID) {
        $sql = "DELETE FROM Usuarios WHERE UsuarioID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuarioID);
        
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    // Listar todos los usuarios
    public function listar() {
        $sql = "SELECT * FROM Usuarios ORDER BY FechaRegistro DESC";
        $resultado = $this->conn->query($sql);
        
        $usuarios = [];
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuarios[] = $fila;
            }
        }
        return $usuarios;
    }
    
    // Buscar usuario por correo
    public function buscarPorCorreo($correo) {
        $sql = "SELECT * FROM Usuarios WHERE Correo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        
        return $usuario;
    }
    
    // Buscar usuario por ID
    public function buscarPorID($usuarioID) {
        $sql = "SELECT * FROM Usuarios WHERE UsuarioID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuarioID);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        
        return $usuario;
    }
    
    public function __destruct() {
        cerrarDB($this->conn);
    }
}
?>