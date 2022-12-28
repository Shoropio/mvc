<?php

namespace App;

class Config
{
    const DB_DSN = DB_TYPE . ":host=". DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
    const DB_USERNAME = "root";
    const DB_PASSWORD = "Shoropio2";
    const DB_OPTIONS = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
    ];
}
