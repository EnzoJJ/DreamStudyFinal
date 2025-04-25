<?php
require_once './app/view/auth.view.php';
require_once './app/model/user.model.php';
require_once './app/helpers/auth.helper.php';

class AuthController
{
    private $view;
    private $model;

    function __construct(){
        $this->view = new AuthView();
        $this->model = new UserModel();
    }

    public function showLogin($error = null){    
        if (AuthHelper::verify()) {
            header('Location: ' . BASE_URL . 'index.php?action=turnos');
            exit();
        }
        $this->view->showLogin($error);
    }

    public function auth() {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $this->showLogin('Faltan datos');
            return;
        }

        $user = $this->model->getByMail($username);
        if ($user && $user->contraseña === $password) {
            AuthHelper::login($user);
            header('Location: ' . BASE_URL . 'index.php?action=turnos');
            exit();
        } else {
            $this->showLogin("Usuario o contraseña incorrectos");
        }
    }
    
    public function logout(){
        AuthHelper::logout();
        header('Location: ' . BASE_URL . 'index.php?action=login');
    }
}
?>
