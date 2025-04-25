<?php

class AuthHelper
{
    public static function init()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }


    public static function login($user) {
        self::init();
        $_SESSION['USER_ID'] = $user->id; // Asegúrate de que este campo sea el correcto
        $_SESSION['USER_EMAIL'] = $user->nombre; // Ajusta según el campo de tu usuario
    }


    public static function logout()
    {
        AuthHelper::init();
        session_destroy();
    }

    public static function verify()
    {
        AuthHelper::init();
        return (isset($_SESSION['USER_ID']));
    }
}
