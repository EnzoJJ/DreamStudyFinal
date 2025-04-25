<?php

class AuthView {
    public function showLogin() {
        require './templates/admin-login.phtml'; // Ajusta la ruta si es necesario
    }

    function showError($error) {
        require './templates/error.phtml';
    }
}