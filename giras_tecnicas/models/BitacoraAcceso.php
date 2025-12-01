<?php
require_once __DIR__ . '/../config/conexion.php';

class BitacoraAcceso {
    private $conn;

    public function __construct() {
        $this->conn = conectarDB();
    }

    // Registrar un acceso
    public function registrar($usuarioID, $ip, $navegador) {
        $sql = "INSERT INTO BitacoraAccesos (UsuarioID, IP, Navegador) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $usuarioID, $ip, $navegador);

        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            $stmt->close();
            return $id;
        }
        $stmt->close();
        return false;
    }

    // Listar todos los accesos con información del usuario
    public function listar($limite = 100) {
        $sql = "SELECT B.AccesoID, B.FechaAcceso, B.IP, B.Navegador, 
                       U.UsuarioID, U.Nombre, U.Correo, U.Rol
                FROM BitacoraAccesos B
                INNER JOIN Usuarios U ON B.UsuarioID = U.UsuarioID
                ORDER BY B.FechaAcceso DESC
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $accesos = [];
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $accesos[] = $fila;
            }
        }
        $stmt->close();

        return $accesos;
    }

    // Listar accesos de un usuario específico
    public function listarPorUsuario($usuarioID, $limite = 50) {
        $sql = "SELECT * FROM BitacoraAccesos 
                WHERE UsuarioID = ? 
                ORDER BY FechaAcceso DESC 
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $usuarioID, $limite);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $accesos = [];
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $accesos[] = $fila;
            }
        }
        $stmt->close();

        return $accesos;
    }

    public function __destruct() {
        cerrarDB($this->conn);
    }
}
?>