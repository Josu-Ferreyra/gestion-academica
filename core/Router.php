<?php
require_once 'core/Auth.php';

class Router {
  /**
   * Array que almacena las rutas registradas para los métodos HTTP GET y POST.
   *
   * @var array
   */
  private $routes = [
    'GET' => [],
    'POST' => []
  ];

  /**
   * Registra una nueva ruta para el método HTTP GET.
   *
   * @param string $uri URI de la ruta.
   * @param string $action Acción en formato 'Controlador@metodo'.
   * @param array $roles (Opcional) Lista de roles autorizados para acceder a la ruta.
   * @return void
   */
  public function get($uri, $action, $roles = []) {
    $this->routes['GET'][$uri] = ['action' => $action, 'roles' => $roles];
  }

  /**
   * Registra una nueva ruta para el método HTTP POST.
   *
   * @param string $uri URI de la ruta.
   * @param string $action Acción en formato 'Controlador@metodo'.
   * @param array $roles (Opcional) Lista de roles autorizados para acceder a la ruta.
   * @return void
   */
  public function post($uri, $action, $roles = []) {
    $this->routes['POST'][$uri] = ['action' => $action, 'roles' => $roles];
  }

  /**
   * Despacha una solicitud HTTP a la acción correspondiente según la URI y el método.
   *
   * @param string $uri URI de la solicitud.
   * @param string $method Método HTTP de la solicitud (GET o POST).
   * @return void
   */
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
