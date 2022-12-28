<?php

namespace App\Models;
use App\Database;
use App\Helpers\Session;
use App\Helpers\Redirect;
use App\Helpers\Password;

// modelo para acceder a la base de datos
class UserModel {

    // atributo para la conexión a la base de datos
    private $db;
    private $session;

    // constructor de la clase
    public function __construct() {
        $this->db = new Database();
        $this->session = new Session;

    }

    function login()
    {

    }

    /*public function perro() {
        // preparar la consulta SQL
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";

        // preparar los parámetros
        $params = [
          ':name' => $name,
          ':email' => $email,
          ':password' => $password
        ];

        // ejecutar la consulta
        $database->query($sql, $params);
    }*/

    //
    public function isLoggedIn() {
        $id = session_id();
        $sql = "SELECT * FROM sessions WHERE id = '$id'";
        $result = $this->db->query($sql);
        return $result->rowCount() > 0;

        header('Location: login.php');
    }

    public function checkIfLoggedIn() {
        // Verificar si hay una sesión activa
        if (isset($_SESSION['shoropio_session_id']) && !empty($_SESSION['shoropio_session_password'])) {
            // Si hay una sesión activa, verificar si el usuario está en la base de datos
            $userId = $_SESSION['shoropio_session_id'];
            $sql = "SELECT * FROM users WHERE id = cleanNumber($userId)";
            $user = $this->db->query($sql);

            if (!empty($user) && md5($user->password ?? '') == $session->get('shoropio_ses_pass')) {
                self::$authCheck = true;
                self::$authUser = $user;
            }
        } else {
            //$this->set('shoropio_session_password', $value);
        }
        // Si no hay una sesión activa o el usuario no está en la base de datos, devolver falso
        return false;
    }

    // Auth check
    function authCheck() {
        // Verificar si hay una sesión activa
        if (isset($_SESSION['shoropio_session_id'])) {
            // Si hay una sesión activa, verificar si el usuario está en la base de datos
            $id_usuario = $_SESSION['shoropio_session_id'];
            $sql = "SELECT * FROM users WHERE id = $id_usuario";
            $result = $this->db->query($sql);
            if ($result->rowCount() > 0) {
                // Si el usuario está en la base de datos, devolver verdadero
                return true;
            }
        }
        // Si no hay una sesión activa o el usuario no está en la base de datos, devolver falso
        return false;
    }
    


    public function countOnline() {
        $expiry = time() - (60 * 60); // sesiones que expiraron hace 1 hora o más
        $sql = "SELECT COUNT(*) FROM sessions WHERE expiry > $expiry";
        $result = $this->db->query($sql);
        return $result->fetchColumn();
    }

    //
    public function getTotalUsers() {
        $sql = "SELECT id FROM users";

        return $this->db->query($sql)->rowCount();
    }

    // métodos del modelo
    public function getAll() {
        $sql = "SELECT * FROM usuarios";
        return $this->db->query($sql);
    }

    public function getById($id) {
        $this->session->set('nombre', 'Juan');
        $sql = "SELECT * FROM users WHERE id = $id";
       // return $this->db->query($sql);

        //return $this->db->query($sql)->fetch();
        //return $this->db->query($sql)->fetchAll();
        // $this->db->lastInsertId();
        //$this->db->rowCount();
        return $this->db->query($sql)->fetchObject();
    }

    public function insert($nombre, $email, $contrasena) {
        $sql = "INSERT INTO usuarios (nombre, email, contrasena) VALUES ('$nombre', '$email', '$contrasena')";
        return $this->db->exec($sql);
    }

    public function update($id, $nombre, $email, $contrasena) {
        $sql = "UPDATE usuarios SET nombre = '$nombre', email = '$email', contrasena = '$contrasena' WHERE id = $id";
        return $this->db->exec($sql);
    }

    public function delete($id) {
        $sql = "DELETE FROM usuarios WHERE id = $id";
        return $this->db->exec($sql);
    }

}


/*

class UserModel {
  private $db;

  public function __construct() {
    // inicializar la conexión a la base de datos
    $this->db = new PDO(/* parámetros de conexión );
  }

  public function login($email, $password) {
    // preparar la consulta para buscar al usuario por su correo electrónico y contraseña
    $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email AND password = :password');
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password', $password);

    // ejecutar la consulta
    $stmt->execute();

    // obtener el usuario resultante
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      // si se encontró un usuario, devolver los datos del usuario
      return $user;
    } else {
      // si no se encontró ningún usuario, devolver false
      return false;
    }
  }
}

*/