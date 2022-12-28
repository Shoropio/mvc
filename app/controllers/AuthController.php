<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Helpers\Response;
use App\Helpers\Session;
use App\Helpers\Redirect;
use App\Helpers\Cookie;
use App\Models\UserModel;
use App\Models\AuthModel;

class AuthController {
    public function __construct() {
        $this->authModel = new AuthModel();
        $this->userModel = new UserModel();
        $this->response = new Response();
        $this->request = new Request();
        $this->session = new Session();

    }

    public function login() {
        // Mostrar el formulario de inicio de sesión
        $data['title'] = 'Bienvenido';
        $data['page'] = 'Bienvenido';
        $data['response'] = new Response();

        echo render('auth/login', $data);
    }

    //
    public function loginPost() {
        // Procesar la solicitud de inicio de sesión
        $username = $this->request->post('username');
        $email = $this->request->post('email');
        $password = $this->request->post('password');

        // Verificar si el nombre de usuario y la contraseña son válidos
        $userModel = new UserModel();
        $user = $this->authModel->getUserByEmail($email);

        if ($user) {
            // Si son válidos, iniciar sesión y redirigir al usuario a la página principal
            //$this->session->set('user', $user);
            $this->session->start();
            
            Redirect::to('/');
        } else {
            // Si no son válidos, mostrar un mensaje de error y volver al formulario de inicio de sesión
            //$error = 'Nombre de usuario o contraseña incorrectos';
            $this->session->setFlashData('error', 'El nombre de usuario o contraseña son incorrectos');
            Redirect::to('/iniciar-sesion');
            //Redirect::with('error', $error);
            //require_once 'views/auth/login.php';
        }
    }

    public function loginsss() {
        // Validar los datos del formulario
        $errors = $this->validate();
        if (count($errors) > 0) {
            // Si hay errores, volver a mostrar el formulario con los mensajes de error
            return view('login', [
                'errors' => $errors
            ]);
        }

        // Obtener el usuario de la base de datos
        $email = $_POST['email'];
        $user = User::getByEmail($email);

        // Verificar la contraseña
        if (Password::verify($_POST['password'], $user->password)) {
            // Si la contraseña es correcta, iniciar sesión
            Session::start();
            Session::set('user_id', $user->id);
            Session::set('name', $user->name);

            // Redirigir al dashboard
            Response::redirect('/dashboard');
        } else {
            // Si la contraseña es incorrecta, volver a mostrar el formulario con un mensaje de error
            return view('login', [
                'errors' => ['La contraseña es incorrecta']
            ]);
        }
    }

    private function validate() {
        $errors = [];

        // Validar el email
        if (!isset($_POST['email']) || strlen($_POST['email']) == 0) {
            $errors[] = 'El email es requerido';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email es inválido';
        } elseif (!$this->isEmailUnique($_POST['email'])) {
            $errors[] = 'El email ya está en uso';
        }

        // Validar el nombre de usuario
        if (!isset($_POST['username']) || strlen($_POST['username']) == 0) {
            $errors[] = 'El nombre de usuario es requerido';
        } elseif (!$this->isUniqueUsername($_POST['username'])) {
            $errors[] = 'El nombre de usuario ya está en uso';
        }

        // Validar la contraseña
        if (!isset($_POST['password']) || strlen($_POST['password']) == 0) {
            $errors[] = 'La contraseña es requerida';
        } elseif (strlen($_POST['password']) < self::MIN_LENGTH) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres';
        }

        return $errors;
    }




}
