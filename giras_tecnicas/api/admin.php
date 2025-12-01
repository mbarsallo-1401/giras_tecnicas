<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Gira.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../models/BitacoraAcceso.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../middleware/auth.php';

// Solo administradores pueden acceder
AuthMiddleware::requireRole('Administrador');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $tipo = $_GET['tipo'] ?? 'all';

    switch ($tipo) {
        case 'usuarios':
            $usuarioModel = new Usuario();
            $usuarios = $usuarioModel->listar();
            Response::success('Usuarios obtenidos', $usuarios);
            break;

        case 'giras':
            $giraModel = new Gira();
            $giras = $giraModel->listar();

            // Agregar total de inscritos a cada gira
            $inscripcionModel = new Inscripcion();
            foreach ($giras as &$gira) {
                $gira['totalInscritos'] = $inscripcionModel->contarPorGira($gira['GiraID']);
            }

            Response::success('Giras obtenidas', $giras);
            break;

        case 'inscripciones':
            $inscripcionModel = new Inscripcion();

            // Obtener todas las inscripciones con información de usuario y gira
            $sql = "SELECT I.InscripcionID, I.FechaInscripcion, I.Estado,
                           U.UsuarioID, U.Nombre as NombreUsuario, U.Correo,
                           G.GiraID, G.Nombre as NombreGira, G.Fecha as FechaGira, G.Lugar
                    FROM Inscripciones I
                    INNER JOIN Usuarios U ON I.UsuarioID = U.UsuarioID
                    INNER JOIN Giras G ON I.GiraID = G.GiraID
                    ORDER BY I.FechaInscripcion DESC";

            require_once __DIR__ . '/../config/conexion.php';
            $conn = conectarDB();
            $resultado = $conn->query($sql);

            $inscripciones = [];
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    $inscripciones[] = $fila;
                }
            }
            cerrarDB($conn);

            Response::success('Inscripciones obtenidas', $inscripciones);
            break;

        case 'bitacora':
            $bitacoraModel = new BitacoraAcceso();
            $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 100;
            $accesos = $bitacoraModel->listar($limite);
            Response::success('Bitácora obtenida', $accesos);
            break;

        case 'all':
        default:
            // Obtener todo
            $usuarioModel = new Usuario();
            $giraModel = new Gira();
            $inscripcionModel = new Inscripcion();
            $bitacoraModel = new BitacoraAcceso();

            $usuarios = $usuarioModel->listar();
            $giras = $giraModel->listar();

            // Agregar total de inscritos
            foreach ($giras as &$gira) {
                $gira['totalInscritos'] = $inscripcionModel->contarPorGira($gira['GiraID']);
            }

            $bitacora = $bitacoraModel->listar(50);

            // Estadísticas
            $stats = [
                'totalUsuarios' => count($usuarios),
                'totalGiras' => count($giras),
                'totalAdministradores' => count(array_filter($usuarios, function($u) {
                    return $u['Rol'] === 'Administrador';
                })),
                'totalEstudiantes' => count(array_filter($usuarios, function($u) {
                    return $u['Rol'] === 'Estudiante';
                })),
                'totalOrganizadores' => count(array_filter($usuarios, function($u) {
                    return $u['Rol'] === 'Organizador';
                }))
            ];

            Response::success('Datos obtenidos', [
                'usuarios' => $usuarios,
                'giras' => $giras,
                'bitacora' => $bitacora,
                'stats' => $stats
            ]);
            break;
    }
}
?>