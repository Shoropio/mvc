<?php

namespace App\Models;
use App\Database;
use App\Helpers\Session;
use App\Helpers\Redirect;
use App\Helpers\Password;

class AuthModel {
    private $db;

    public function __construct() {
        // Inicializar la conexión a la base de datos
        $this->db = new Database();
    }
    
    public function login($username, $password) {
        // Consultar el usuario con el nombre de usuario especificado
        $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = $this->db->query($sql);

        // Si no se encuentra el usuario, devolver null
        if ($result->rowCount() == 0) {
            return null;
        }

        // Obtener el usuario encontrado
        $user = $result->fetch();

        // Verificar si la contraseña es válida
        if (Password::verify($password, $user['password'])) {
            // Si la contraseña es válida, devolver el usuario
            return $user;
        } else {
            // Si la contraseña no es válida, devolver null
            return null;
        }
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = $id";
        $result = $this->db->query($sql);
        if ($result->rowCount() > 0) {
            return $result->fetch();
        }
        return null;
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->db->query($sql);
        if ($result->rowCount() > 0) {
            return $result->fetch();
        }
        return false;
    }

    public function getUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $this->db->query($sql);
        if ($result->rowCount() > 0) {
            return $result->fetch();
        }
        return null;
    }

    // Método para recordar al usuario en el siguiente inicio de sesión
    public function rememberMe($userId) {
        // Generar un token aleatorio y encriptarlo
        $token = bin2hex(random_bytes(16));
        $tokenHash = Password::hash($token);

        // Establecer una cookie con el token
        setcookie('remember_me', $token, time() + 60 * 60 * 24 * 30, '/');

        // Almacenar el token en la base de datos
        $sql = "UPDATE users SET remember_me_token = '$tokenHash' WHERE id = $userId";
        $this->db->ejecutarActualizacion($sql);
    }

    // Método para generar un nombre de usuario único
    public function generateUniqueUsername($username) {
        // Verificar si el nombre de usuario ya existe
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $this->db->query($sql);
        if ($result->rowCount() > 0) {
            // Si el nombre de usuario ya existe, generar uno nuevo agregando un número al final
            $i = 1;
            while (true) {
                $newUsername = $username . $i;
                $sql = "SELECT * FROM users WHERE username = '$newUsername'";
                $result = $this->db->query($sql);
                if ($result->rowCount() == 0) {
                    return $newUsername;
                }
                $i++;
            }
        } else {
            // Si el nombre de usuario es único, retornarlo
            return $username;
        }
    }

    public function generateUniqueSlug($slug, $id = null) {
        // Verificar si el slug ya existe en la base de datos
        $sql = "SELECT * FROM users WHERE slug = '$slug'";
        if ($id) {
            // Si se especificó un ID, excluirlo de la verificación
            $sql .= " AND id != $id";
        }
        $result = $this->db->query($sql);
        if ($result->rowCount() > 0) {
            // Si el slug ya existe, agregar un número al final para hacerlo único
            $i = 2;
            while (true) {
                $newSlug = $slug . '-' . $i;
                $sql = "SELECT * FROM users WHERE slug = '$newSlug'";
                if ($id) {
                    $sql .= " AND id != $id";
                }
                $result = $this->db->query($sql);
                if ($result->rowCount() == 0) {
                    return $newSlug;
                }
                $i++;
            }
        }
        return $slug;
    }

    public function deleteUseur($id) {
        // Eliminar el usuario de la base de datos
        $sql = "DELETE FROM users WHERE id = $id";
        $this->db->ejecutarActualización($sql);
    }

    public function logout() {
        // Destruir la sesión del usuario
        session_destroy();
    }

    public function isSlugUnique($slug) {
        $sql = "SELECT * FROM users WHERE slug = '$slug'";
        $result = $this->db->query($sql);
        return $result->rowCount() == 0;
    }

    public function isEmailUnique($email) {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->db->query($sql);
        return $result->rowCount() == 0;
    }

    public function isUniqueUsername($username) {
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $this->db->query($sql);
        return $result->rowCount() == 0;
    }

    public function updateLastSeen($userId) {
        $lastSeen = time();
        $sql = "UPDATE users SET last_seen = $lastSeen WHERE id = $userId";
        $this->db->ejecutarActualizacion($sql);
    }

    public function getUsersCount() {
        $sql = "SELECT COUNT(*) FROM users";
        $result = $this->db->query($sql);
        $row = $result->fetch();
        return $row[0];
    }

    public function deleteUser($userId) {
        $sql = "DELETE FROM users WHERE id = $userId";
        $this->db->ejecutarActualizacion($sql);
    }




}
