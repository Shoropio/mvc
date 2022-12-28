<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Helpers\Session;

// controlador para manejar la lógica de negocio
class UserController {

  // modelo para acceder a la base de datos
  private $model;

  // constructor de la clase
  public function __construct() {
    $this->model = new UserModel();
  }

  public function view()
  {
    $perro = $this->model->getTotalUsers();
    $data = [
          ['id' => 1, 'name' => 'John Doe'],
          ['id' => 2, 'name' => 'ggg'],
          ['id' => 3, 'name' => $perro],
        ];
        echo view('usuarios/list', $data);
  }

  public function login() {
    // obtener los datos de inicio de sesión proporcionados por el usuario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // instanciar el modelo de usuario
    $userModel = new UserModel();

    // intentar iniciar sesión con los datos proporcionados
    $user = $userModel->login($email, $password);

    if ($user) {
      // si las credenciales son válidas, iniciar la sesión y redirigir al usuario a la página principal
      session_start();
      $_SESSION['user'] = $user;
      header('Location: /');
      exit;
    } else {
      // si las credenciales son inválidas, mostrar un mensaje de error y permitir que el usuario intente de nuevo
      echo 'Las credenciales proporcionadas son inválidas. Por favor, intente de nuevo.';
    }
  }
}
/*
  // método para mostrar la lista de usuarios
  public function list() {
    // código para obtener la lista de usuarios desde la base de datos
    $usuarios = // ...

    // cargar la vista y pasar los datos a la vista
    require_once 'views/usuarios/list.php';
  }

  // métodos del controlador
  public function getAll() {
    return $this->model->getAll();
  }

  public function getById($id) {
    return $this->model->getById($id);
  }

  public function insert($nombre, $email, $contrasena) {
    return $this->model->insert($nombre, $email, $contrasena);
  }

  public function update($id, $nombre, $email, $contrasena) {
    return $this->model->update($id, $nombre, $email, $contrasena);
  }

  public function delete($id) {
    return $this->model->delete($id);
  }

}*/

/*

$router = new Router();

$router->addRoute('GET', '/', function() {
  // código para mostrar la página principal
});

$router->addRoute('GET', '/usuarios', function() {
  // código para mostrar la lista de usuarios
});

$router->addRoute('GET', '/usuarios/([0-9]+)', function($id) {
  // código para mostrar el detalle de un usuario con el ID especificado
});

$router->run();


*/

/*

<?php

namespace App\Controllers;

use PDO;

class UserController
{
  // atributo para almacenar la conexión a la base de datos
  protected $db;

  // constructor para inicializar la conexión a la base de datos
  public function __construct()
  {
    // crear una nueva conexión a la base de datos utilizando PDO
    $this->db = new PDO('mysql:host=localhost;dbname=mi_base_de_datos', 'usuario', 'contraseña');
  }

  // método para listar a los usuarios
  public function list()
  {
    // ejecutar una consulta SELECT para obtener a los usuarios de la base de datos
    $stmt = $this->db->query('SELECT * FROM usuarios');

    // recuperar a los usuarios como una matriz asociativa
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // mostrar la vista con la lista de usuarios
    require_once 'views/usuarios/list.php';
  }
}




*/