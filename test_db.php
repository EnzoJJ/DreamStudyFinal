<?php
$servername = "localhost";
$username = "u765886106_Leo";
$password = "DreamStudy1234";
$dbname = "u765886106_peluquerialeo";

try {
    $db = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ **Conexión exitosa a la base de datos.**";
} catch (PDOException $e) {
    die("🛑 **Error de conexión:** " . $e->getMessage());
}
?>
