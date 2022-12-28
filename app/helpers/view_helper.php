<?php

// función para cargar una vista
/*function view($viewName, $data = [])
{
    // extraer los datos como variables individuales
    extract($data);

    // cargar la vista
    require_once APP_PATH . "views/{$viewName}.php";
}*/


function render($view, $data = []) {
    // Construir la ruta completa al archivo de la vista
    $viewPath = __DIR__ . '/../views/' . $view . '.php';

    // Verificar si el archivo de la vista existe
    if (!file_exists($viewPath)) {
        throw new Exception('La vista no existe');
    }

    // Extraer las variables del array $data como variables independientes
    extract($data);

    // Incluir el archivo de la vista
    require $viewPath;
}
