<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/models/Usuario.php';

$url = $_GET['url'] ?? 'auth';
$parts = explode('/', trim($url, '/'));

// Determino controlador y acción
$controllerName = ucfirst($parts[0]) . 'Controller';
$action = $parts[1] ?? 'showLogin';

// Instancio y ejecuto
$controller = new $controllerName();
if (method_exists($controller, $action)) {
  $controller->{$action}();
} else {
  header("HTTP/1.0 404 Not Found");
  echo "Página no encontrada";
}
