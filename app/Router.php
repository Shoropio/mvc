<?php

namespace App;

class Router
{
    protected $routes = [];

    public function addRoute($method, $pattern, $callback)
    {
        $this->routes[] = [$method, $pattern, $callback];
    }

    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            list($routeMethod, $routePattern, $routeCallback) = $route;

            if ($method == $routeMethod) {
                $matches = [];
                if (preg_match_all('#^' . $routePattern . '$#', $uri, $matches, PREG_SET_ORDER)) {
                    $params = array_slice($matches[0], 1);
                    return call_user_func_array($routeCallback, $params);
                }
            }
        }

        // manejo de rutas no encontradas
        //header('HTTP/1.1 404 Not Found');
        //echo '404 Not Found';

        // manejo de rutas no encontradas
        header('HTTP/1.1 404 Not Found');
        $errorController = new Controllers\HomeController();
        $errorController->error404();
    }
}
