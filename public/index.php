<?php
session_start();

// Carga de configuraciones y dependencias
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AlumnoController.php';
require_once __DIR__ . '/../app/controllers/ProfesorController.php';

/**
 * Función para manejar el enrutamiento de la aplicación.
 */
function handleRouting($url) {
  $url = filter_var($url, FILTER_SANITIZE_URL);
  $parts = explode('/', trim($url, '/'));

  $controllerName = ucfirst($parts[0] ?? 'auth') . 'Controller';
  $action = $parts[1] ?? 'loadView';

  if (!class_exists($controllerName)) {
    send404("Controlador no encontrado: $controllerName");
    return;
  }

  $controller = new $controllerName();

  if (!method_exists($controller, $action)) {
    send404("Método no encontrado: $action en $controllerName");
    return;
  }

  $controller->{$action}();
}

/**
 * Función para enviar una respuesta 404.
 */
function send404($message) {
  header("HTTP/1.0 404 Not Found");
  echo "Página no encontrada: $message";
  exit;
}

// Obtener la URL y manejar el enrutamiento
$url = $_GET['url'] ?? 'auth';
handleRouting($url);
