<?php

namespace App;

class Cookie {
    // nombre de la cookie
    private $name;

    // duración de la cookie en segundos
    private $duration;

    public function __construct($name, $duration) {
        $this->name = $name;
        $this->duration = $duration;
    }

    // crear la cookie de inicio de sesión
    public function create($user) {
        setcookie($this->name, serialize($user), time() + $this->duration, '/');

        /* setcookie($this->name, serialize($user), time() + $this->duration, '/', '', false, true);
     */
    }

    // obtener el usuario almacenado en la cookie
    public function get() {
        if (isset($_COOKIE[$this->name])) {
          return unserialize($_COOKIE[$this->name]);
        } else {
          return false;
        }
    }

    // eliminar la cookie de inicio de sesión
    public function delete() {
        setcookie($this->name, '', time() - 3600, '/');
    }
}
