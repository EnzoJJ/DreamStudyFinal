<?php
class TurnoModel {
    private $db;

    public function __construct() { 
        $servername = "localhost"; // Host correcto de la base de datos
        $username = "root"; // Usuario correcto
        $password = ""; // Contraseña configurada
        $dbname = "peluqueria_leo"; // Nombre de la base de datos

    try {
        $this->db = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error de conexión a la base de datos: " . $e->getMessage());
    }
}


    // Verificar si un turno ya está reservado para una fecha y horario específicos
    public function verificarTurnoReservado($fecha, $horario) {
        $query = $this->db->prepare('SELECT * FROM turnos WHERE fecha = ? AND horario = ?');
        $query->execute([$fecha, $horario]);
        return $query->fetch(PDO::FETCH_ASSOC); // Devuelve true si existe
    }

    // Guardar un nuevo turno en la base de datos
    public function guardarTurno($nombre, $apellido, $telefono, $email, $servicio, $fecha, $horario) {
        $query = $this->db->prepare('INSERT INTO turnos (nombre, apellido, telefono, email, servicio, fecha, horario) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$nombre, $apellido, $telefono, $email, $servicio, $fecha, $horario]);
    }
    public function obtenerTurnos() {
        $query = $this->db->prepare('SELECT * FROM turnos');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }  
    public function getHorariosReservadosPorFecha($fecha) {
        $query = "SELECT horario FROM turnos WHERE fecha = :fecha"; // Ajusta esto a tu esquema de base de datos
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
    
        // Retorna los horarios reservados en un array plano
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []; // Asegúrate de retornar un array vacío si no hay resultados
    }
    public function deleteTurno($turno_id) {
        $query = $this->db->prepare('DELETE FROM turnos WHERE id_turno = :turno_id');
        $query->bindParam(':turno_id', $turno_id, PDO::PARAM_INT);
        
        if ($query->execute()) {
            return true; 
        } else {
            error_log("Error al eliminar el turno: " . implode(", ", $query->errorInfo())); // Manejo de errores
            return false; 
        }
    }
    public function eliminarTurno($fecha, $horario) {
        $sql = "DELETE FROM turnos WHERE fecha = ? AND horario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fecha, $horario]);
    }
    public function guardarDiaBloqueado($fecha) {
        $query = $this->db->prepare('INSERT INTO dias_bloqueados (fecha) VALUES (?)');
        return $query->execute([$fecha]);
    }
    public function reservarTodosLosTurnosDeDia($fecha) {
        // Asume que hay un horario predefinido para los turnos
        $horarios = ["14:00",
        "14:40",
        "15:20",
        "16:00",
        "16:40",
        "17:20",
        "18:00",
        "18:40",
        "19:20",
        "20:00",];
        foreach ($horarios as $horario) {
            $query = "INSERT INTO turnos (nombre, apellido, email, telefono, servicio, fecha, horario)
                      VALUES ('Cerrado', '', '', '', 'Día bloqueado', :fecha, :horario)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':horario', $horario);
            $stmt->execute();
        }
    }
    public function cancelarTurno($turno_id) {
    // Realiza la consulta SQL para eliminar el turno por su ID
    $sql = "DELETE FROM turnos WHERE id_turno = :turno_id";
    
    // Preparar la consulta
    $stmt = $this->db->prepare($sql);
    
    // Ejecutar la consulta con el ID del turno
    return $stmt->execute([':turno_id' => $turno_id]);
}


    public function getLastInsertedTurnoId() {
        return $this->db->lastInsertId();
    }
    public function obtenerTurnoPorId($turno_id) {
    $query = "SELECT * FROM turnos WHERE id_turno = :turno_id"; // Cambié 'id' por 'id_turno'
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':turno_id', $turno_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve un solo turno o null si no lo encuentra
}



    
    
}
?>
