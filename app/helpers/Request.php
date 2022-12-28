<?php

namespace App\Helpers;

class Request 
{
    private $data;

    public function __construct() {
        // Obtener los datos del POST y GET
        $this->data = array_merge($_POST, $_GET);
    }

    public function post($key, $default = null) {
        // Obtener un valor del POST
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function get($key, $default = null) {
        // Obtener un valor del GET
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function all() {
        // Obtener todos los datos del POST y GET
        return $this->data;
    }

    public static function getMethod() {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public static function isPost() {
        return self::getMethod() === 'POST';
    }

    public static function isGet() {
        return self::getMethod() === 'GET';
    }

    public static function getBody() {
        return file_get_contents('php://input');
    }

    /*

    public static function getBody() {
        // Si el contenido de la solicitud es JSON, decodificarlo y devolverlo como un array
        if (self::getContentType() === 'application/json') {
            return json_decode(file_get_contents('php://input'), true);
        }
        // Si el contenido de la solicitud es de otro tipo, devolver los datos como un array
        return $_POST;
    }

    */

    public static function getQueryParam($key) {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    public static function getPostParam($key) {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    public static function getContentType() {
        return $_SERVER['CONTENT_TYPE'];
    }
}
