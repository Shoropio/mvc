<?php

namespace App\Helpers;

use App\Cookie;
use App\Database;
use App\Helpers\Redirect;
use App\Helpers\Password;

class Session {

    // atributos de la clase
    public  $loggedIn;
    public static $authCheck = false;
    public static $authUser = null;
    private $db;
    private $table = 'sessions';
    private $sessionId;
    private $data;
    private $cookie;

    public function __construct() {
        // Inicializar la conexión a la base de datos
        $this->db = new Database();
        //Redirect::to('/login');

        //$this->startSession();
    }

    //
    public function startSession() {
        /* Determine if user is logged in */
        $this->loggedIn = $this->checkIfLoggedIn();

        if ($this->loggedIn) {

        }
    }

    public function startt() {
        // Iniciar la sesión PHP
        session_start();

        

        // Verificar si hay una sesión activa en la base de datos
        $id = session_id();
        $sql = "SELECT * FROM sessions WHERE id = '$id'";
        $result = $this->db->query($sql);
        if ($result->rowCount() > 0) {
            // Si hay una sesión activa, actualizar el tiempo de expiración
            $expiry = time() + (60 * 60); // 1 hora de vida
            $sql = "UPDATE sessions SET expiry = $expiry WHERE id = '$id'";
            $this->db->exec($sql);
        } else {
            // Si no hay una sesión activa, crear una nueva en la base de datos
            $data = '{}';
            $expiry = time() + (60 * 60); // 1 hora de vida
            $sql = "INSERT into sessions (id, data, expiry) VALUES ('$id', '$data', $expiry)";
            $this->db->query($sql);
        }
    }

    

    /*
    public function checkIfLoggedIn() {
        // Verificar si hay una sesión activa
        if (isset($_SESSION['session'])) {
            // Si hay una sesión activa, verificar si el usuario está en la base de datos
            $id_usuario = $_SESSION['id_usuario'];
            $sql = "SELECT * FROM usuarios WHERE id = $id_usuario";
            $result = $this->db->query($sql);
            if ($result->rowCount() > 0) {
                // Si el usuario está en la base de datos, devolver verdadero
                return true;
            }
        }
        // Si no hay una sesión activa o el usuario no está en la base de datos, devolver falso
        return false;
    }

    */

    public function set($key, $value) {
        // Establecer un valor en la sesión PHP
        $_SESSION[$key] = $value;

        // Obtener el valor actual de la sesión PHP
        $data = json_encode($_SESSION);

        // Actualizar la sesión en la base de datos
        $id = session_id();
        $sql = "UPDATE sessions SET data = '$data' WHERE id = '$id'";
        $this->db->exec($sql);
    }

    public function get($key) {
        // Obtener un valor de la sesión PHP
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }

    public function destroy() {
        // Destruir la sesión PHP
        session_destroy();

        // Eliminar la sesión de la base de datos
        $id = session_id();
        $sql = "DELETE FROM sessions WHERE id = '$id'";
        $this->db->exec($sql);

        // Establecer la cookie de sesión con una fecha de expiración en el pasado
        setcookie(session_name(), '', time() - 3600);
    }

    public function regenerate() {
        // Regenerar el ID de sesión PHP
        session_regenerate_id(true);

        // Obtener el nuevo ID de sesión
        $id = session_id();

        // Actualizar la sesión en la base de datos
        $sql = "UPDATE sessions SET id = '$id' WHERE id = '$id'";
        $this->db->exec($sql);
    }

    public function setFlashData($key, $value) {
        // Establecer un valor temporal en la sesión PHP
        $_SESSION['flash'][$key] = $value;
    }

    public function getFlashData($key) {
        // Obtener el valor temporal de la sesión PHP
        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $value;
        }
        return null;
    }

    public function setMessage($key, $value) {
        $this->set($key, $value);
    }


    public function removeMessage($key) {
        $this->remove($key);
    }

    private function remove($key) {
        session_start();
        // Eliminar el valor de la sesión PHP
        unset($_SESSION[$key]);

        // Obtener el valor actual de la sesión PHP
        $data = json_encode($_SESSION);

        // Actualizar la sesión en la base de datos
        $id = session_id();
        $sql = "UPDATE sessions SET data = '$data' WHERE id = '$id'";
        $this->db->exec($sql);
    }





}
