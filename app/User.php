<?php

class User {

  // atributos de la clase
  private $id;
  private $nombre;
  private $email;
  private $contrasena;

  // constructor de la clase
  public function __construct($id, $nombre, $email, $contrasena) {
    $this->id = $id;
    $this->nombre = $nombre;
    $this->email = $email;
    $this->contrasena = $contrasena;
  }

  // métodos de la clase
  public function getId() {
    return $this->id;
  }

  public function getNombre() {
    return $this->nombre;
  }

  public function getEmail() {
    return $this->email;
  }

  public function getContrasena() {
    return $this->contrasena;
  }

  public function setNombre($nombre) {
    $this->nombre = $nombre;
  }

  public function setEmail($email) {
    $this->email = $email;
  }

  public function setContrasena($contrasena) {
    $this->contrasena = $contrasena;
  }

}

