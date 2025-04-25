<?php
class Conexion {
    public static function getConnection() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "peluquerialeo";

        // Crear la conexión
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar la conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        return $conn;
    }
}
?>
