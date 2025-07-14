<?php
require_once 'core/Auth.php';

class Router {

  private $routes = [
    'GET' => [],
    'POST' => []
  ];


  public function get($uri, $action, $roles = []) {
    $this->routes['GET'][$uri] = ['action' => $action, 'roles' => $roles];
  }

  public function post($uri, $action, $roles = []) {
    $this->routes['POST'][$uri] = ['action' => $action, 'roles' => $roles];
  }

  public function dispatch($uri, $method) {
    $uri = parse_url($uri, PHP_URL_PATH);

    if (!isset($this->routes[$method][$uri])) {
      http_response_code(404);
      echo "404 - Página no encontrada";
      return;
    }

    $route = $this->routes[$method][$uri];

    if (!empty($route['roles']) && !Auth::checkRoles($route['roles'])) {
      http_response_code(403);
      echo "403 - Acceso no autorizado";
      return;
    }

    list($controllerName, $methodName) = explode('@', $route['action']);

    require_once "controllers/{$controllerName}.php";

    $controller = new $controllerName();

    if (!method_exists($controller, $methodName)) {
      http_response_code(500);
      echo "500 - Método no definido";
      return;
    }

    call_user_func([$controller, $methodName]);
  }
}
