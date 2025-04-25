<?php 
require_once './database/config.php';
class Model {
    protected $db;

    function __construct() { // Cambio __contruct a __construct
        $this->db = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB . ';charset=utf8', MYSQL_USER, MYSQL_PASS);
    }
}
