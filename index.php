<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './app/controller/turno.controller.php';
require_once './app/controller/auth.controller.php';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
define('BASE_URL', $protocol . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/');

$action = 'reservar'; // Acción por defecto
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

$params = explode('/', $action);

$controller = new TurnoController();
$authController = new AuthController();

switch ($params[0]) {
    case 'reservar':
        $controller->mostrarFormulario();
        break;

    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->reservar();
        } else {
            echo "Error: Datos del formulario no recibidos correctamente.";
        }
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->auth();
        } else {
            $authController->showLogin();
        }
        break;

    case 'turnos':
        if (!AuthHelper::verify()) {
            header('Location: ' . BASE_URL . 'index.php?action=login');
            exit();
        }
        $controller->showTurnos();
        break;

    case 'logout':
        $authController->logout();
        break;

    // Nueva ruta para manejar la solicitud AJAX
    case 'obtenerHorariosReservados':
        $controller->obtenerHorariosReservados();
        break;
        case 'delete':
            $controller->cancelarTurno();
            break;
        
    case 'cancelTurnoCliente':
        // Para el cliente
        $controller->cancelar();
        break;   
    case 'reservarTodoDia':
        $controller->reservarTodoDia();
        break;
    case 'cancelar':
        $controller->cancelarById();
        break;

        
        
    default:
        echo "404 - Página no encontrada";
        break;
}
?>

