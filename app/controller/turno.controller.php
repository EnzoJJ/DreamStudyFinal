<?php
require_once './app/model/turno.model.php';
require_once './app/helpers/auth.helper.php';
require_once './app/view/turno.view.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


class TurnoController {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new TurnoModel();
        $this->view = new TurnoView();
    }

    public function mostrarFormulario() {
        $this->view->showForm(); // Llama a la vista que muestra el formulario
    }
    
    public function reservar() { 
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $servicio = $_POST['servicio'];
    $fecha = $_POST['fecha'];
    $horario = $_POST['horario'];

    // Verificar si el turno ya está reservado
    if ($this->model->verificarTurnoReservado($fecha, $horario)) {
        $this->view->formFallido();
        return;
    }

    // Reservar el turno
    $this->model->guardarTurno($nombre, $apellido, $telefono, $email, $servicio, $fecha, $horario);
    
    // Obtener el ID del último turno insertado
    $turno_id = $this->model->getLastInsertedTurnoId(); // Este método debe devolver el ID del último turno guardado



    // Enviar email con PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP (Gmail en este caso)
        $mail->SMTPAuth = true;
        $mail->Username = 'dreamstudypeluqueria@gmail.com'; // Tu email
        $mail->Password = 'tgie cjqv vgjd tpoy'; // Tu contraseña (usa una clave de aplicación en Gmail)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configurar el remitente y destinatario
        $mail->setFrom('dreamstudypeluqueria@gmail.com', 'DreamStudy');
        $mail->addAddress($email, "$nombre $apellido");

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = "Confirmacion de turno - DreamStudy";
        $mail->Body = "
            <h2>Hola $nombre $apellido,</h2>
            <p>Tu turno para <strong>$servicio</strong> ha sido reservado con éxito.</p>
            <p><strong>Fecha:</strong> $fecha</p>
            <p><strong>Horario:</strong> $horario</p>
            <p>Si deseas cancelar tu turno, haz clic en el siguiente enlace:</p>
            <a href='" . rtrim(BASE_URL, '/') . "/index.php?action=cancelar&turno_id=$turno_id'>Cancelar mi turno</a>
            <br>
            <p>Gracias por elegirnos.</p>
        ";



        // Enviar correo
        $mail->send();

    } catch (Exception $e) {
        error_log("Error al enviar el correo: {$mail->ErrorInfo}");
    }

    $this->view->formExitoso([
        'nombre' => $nombre,
        'apellido' => $apellido,
        'servicio' => $servicio,
        'fecha' => $fecha,
        'horario' => $horario,
    ]);
    exit();
}

    public function showTurnos() {
        if (!AuthHelper::verify()) {
            header('Location: ' . BASE_URL . 'index.php?action=login');
            exit();
        }
    
        // Obtener los turnos desde el modelo
        $turnos = $this->model->obtenerTurnos();
    
        // Inicializar un array para agrupar los turnos por fecha
        $turnosAgrupados = [];
        $diasEnEspanol = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' =>'Domingo'
        ];
    
        // Agrupar los turnos por fecha exacta
        foreach ($turnos as $turno) {
            $diaIngles = date('l', strtotime($turno->fecha)); 
            $diaEspanol = $diasEnEspanol[$diaIngles];
            $fechaFormateada = date('d-m-Y', strtotime($turno->fecha)); // Fecha en formato dd-mm-aaaa
    
            // Crear una clave que combine el día en español y la fecha
            $diaYFecha = $diaEspanol . ' ' . $fechaFormateada;
            $turnosAgrupados[$diaYFecha][] = $turno;
        }
    
        // Ordenar los turnos por fecha ascendente
        uksort($turnosAgrupados, function($a, $b) {
            // Extraer las fechas de los strings "Lunes 01-01-2024"
            $fechaA = DateTime::createFromFormat('d-m-Y', substr($a, strpos($a, ' ') + 1));
            $fechaB = DateTime::createFromFormat('d-m-Y', substr($b, strpos($b, ' ') + 1));
            return $fechaA <=> $fechaB; // Comparar las fechas
        });
    
        // Pasar los turnos agrupados y ordenados a la vista
        $this->view->showTurnos($turnosAgrupados);
    }
    
    

    // Método para obtener horarios disponibles
    public function obtenerHorariosReservados() {
        if (isset($_GET['fecha'])) {
            $fecha = $_GET['fecha'];
    
            // Llama al modelo para obtener los horarios reservados
            $horariosReservados = $this->model->getHorariosReservadosPorFecha($fecha);
    
            // Formatear los horarios reservados para solo obtener la parte de la hora
            $horariosReservados = array_map(function($horario) {
                return date('H:i', strtotime($horario)); // Formato 'HH:MM'
            }, $horariosReservados);
    
            // Lista de horarios posibles
            $todosLosHorarios = ['14:00', '14:40', '15:20', '16:00', '16:40', '17:20', '18:00', '18:40', '19:20', '20:00'];
    
            // Filtrar los horarios disponibles
            $horariosDisponibles = array_diff($todosLosHorarios, $horariosReservados);
    
            // Devolver los horarios disponibles como JSON
            header('Content-Type: application/json');
            echo json_encode(array_values($horariosReservados)); // Cambia esto para devolver solo los reservados si es necesario
            exit();
        }
    }
    public function cancelarTurno() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener el ID del turno a cancelar
            $turno_id = $_POST['turno_id'];
            error_log("ID del turno a cancelar: " . $turno_id); // Verificación
    
            // Llamar al modelo para eliminar el turno
            if ($this->model->deleteTurno($turno_id)) {
                $_SESSION['mensaje'] = "Turno cancelado con éxito.";
            } else {
                $_SESSION['mensaje'] = "Error al cancelar el turno.";
            }
            header('Location: index.php?action=turnos');
            exit;
        }
    }
    public function cancelar() {
        $fecha = $_GET['fecha'];
        $horario = $_GET['horario'];
    
        $this->model->eliminarTurno($fecha, $horario);
        header("Location: index.php?action=reservar");
        exit();
    }
    public function reservarTodoDia() {
        if (isset($_POST['fecha'])) {
            $fecha = $_POST['fecha'];
            $this->model->reservarTodosLosTurnosDeDia($fecha);
            $_SESSION['mensaje'] = "Todos los turnos del día $fecha han sido reservados.";
        } else {
            $_SESSION['mensaje'] = "No se pudo reservar el día.";
        }
        header('Location: index.php?action=turnos');
        exit();
    }
    public function cancelarById() {
    // Verificar si el turno_id está en la URL
    if (isset($_GET['turno_id'])) {
        $turno_id = $_GET['turno_id'];

        // Llamar al modelo para cancelar el turno usando el turno_id
        $resultado = $this->model->cancelarTurno($turno_id);

        if ($resultado) {
            // Si la cancelación es exitosa, redirigir a la página de turnos
            $_SESSION['mensaje'] = "Turno cancelado exitosamente.";
            $this->view->showCancelacion();
            exit();
        } else {
            // Si algo falla en la cancelación, mostrar un mensaje de error
            $_SESSION['mensaje'] = "No se pudo cancelar el turno. Intenta nuevamente.";
            header('Location: index.php?action=reservar');
            exit();
        }
    } else {
        // Si no se pasa el turno_id en la URL, redirigir a la página de reservas con mensaje de error
        $_SESSION['mensaje'] = "No se pudo encontrar el turno a cancelar.";
        header('Location: index.php?action=reservar');
        exit();
    }
}



}
?>
