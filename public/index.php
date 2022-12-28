<?php

// Check PHP version.
$minPhpVersion = '7.4'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );

    exit($message);
}

// definir la constante ENVIRONMENT
define('ENVIRONMENT', 'development');

// incluir el archivo autoload.php
require_once '../autoload.php';

//
if (ENVIRONMENT === 'development') {
// Whoops
  $whoops = new Whoops\Run;
  $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);
  $whoops->register();
} else {
  // configurar el manejador de errores en el entorno de producción
  // ...
}

// instanciar la clase Router
$router = new App\Router();

// añadir las rutas de la aplicación
/* $router->addRoute('GET', '/', function() {
  $controller = new App\Controllers\HomeController();
  $controller->index();
});

$router->addRoute('GET', '/usuarios/', function() {
  $controller = new App\Controllers\UserController();
  $controller->list();
});

$router->addRoute('GET', '/usuariosd\/(\d+)/', function($id) {
  // código para manejar la ruta '/usuarios/123' con el parámetro $id='123'
});*/

// añadir las rutas de la aplicación
$router->addRoute('GET', '/', function() {
  $controller = new App\Controllers\HomeController();
  $controller->index();
});

$router->addRoute('GET', '/usuarios/{id}', function($id) {
  $controller = new App\Controllers\UserController();
  $controller->view($id);
});

$router->addRoute('GET', '/iniciar-sesion', function() {
    // código para mostrar el detalle de un usuario con el ID especificado
    $controller = new App\Controllers\AuthController();
    $controller->login();
});

$router->addRoute('GET', '/usuarios/([0-9]+)', function($id) {
  // código para mostrar el detalle de un usuario con el ID especificado
  $controller = new App\Controllers\UserController();
  $controller->view($id);
});

// Post
$router->addRoute('POST', '/login-post', function() {
  // código para mostrar el detalle de un usuario con el ID especificado
    $controller = new App\Controllers\AuthController();
    $controller->loginPost();
});

$router->addRoute('POST', '/usuarios', function() {
  // código para manejar la ruta /usuarios con método POST
});

// ejecutar la ruta
$router->run();
