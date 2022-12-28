<?php

namespace App;
use App\Config;
use PDO;

class Database {

    // atributos de la clase
    private $pdo;
    private $dsn = Config::DB_DSN;
    private $usuario = Config::DB_USERNAME;
    private $contrasena = Config::DB_PASSWORD;
    private $opciones = Config::DB_OPTIONS;

    // constructor de la clase
    public function __construct() {
        try {
            $this->pdo = new \PDO($this->dsn, $this->usuario, $this->contrasena, $this->opciones);
        } catch (\PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
    }

    //
    public function query($sql)
    {
        return $this->pdo->query($sql);
    }

    //
    public function exec($sql)
    {
        return $this->pdo->exec($sql);
    }

    //
    public function prepare($sql, $data)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $stmt->fetchAll();
    }

}
