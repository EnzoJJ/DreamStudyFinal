<?php
require_once './database/model.php';

class UserModel extends Model {
    function __construct() {
        parent::__construct();
    }

    public function getByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE nombre = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_OBJ); // Devuelve el usuario y la contraseña hasheada
    }
}
?>
