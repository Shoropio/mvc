<?php

session_start();

// definir la constante ROOT_PATH
//define('ROOT_PATH', realpath(__DIR__) . '/');
define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
const APP_PATH = ROOT_PATH . 'app/';
// configuración de la base de datos
define("DB_TYPE", "mysql");
define('DB_HOST', 'localhost');
define('DB_NAME', 'mvc');
define('DB_USER', 'root');
define('DB_PASS', 'Shoropio2');

// otras configuraciones
define('URL_BASE', 'http://localhost/aplicacion');
define('NOMBRE_APLICACION', 'Mi Aplicación');

// Helpers
require_once ROOT_PATH . 'app/helpers/utils_helper.php';
require_once ROOT_PATH . 'app/helpers/view_helper.php';

/* Autoload for vendor */
require_once ROOT_PATH . 'vendor/autoload.php';

// configurar la función autoload
function autoload($className) {
    // construir la ruta completa del archivo utilizando la constante ROOT_PATH y siguiendo el estándar de nombres de espacio de PHP (PSR-4)
    $file = ROOT_PATH . str_replace('\\', '/', $className) . '.php';

    // cargar el archivo si existe
    if (file_exists($file)) {
        require_once $file;
    }
}

// registrar la función de autoload
spl_autoload_register('autoload');
