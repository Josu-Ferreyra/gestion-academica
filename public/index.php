<?php
session_start();

// Carga de configuraciones y dependencias
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AlumnoController.php';
require_once __DIR__ . '/../app/controllers/ProfesorController.php';
require_once __DIR__ . '/../app/controllers/MateriaController.php';

/**
 * Función para manejar el enrutamiento de la aplicación.
 */
function handleRouting($url) {
  $url = filter_var($url, FILTER_SANITIZE_URL);
  $parts = explode('/', trim($url, '/'));

  $controllerName = ucfirst($parts[0] ?? 'auth') . 'Controller';
  $action = $parts[1] ?? 'loadView';
  $params = array_slice($parts, 2);

  if (!class_exists($controllerName)) {
    send404("Controlador no encontrado: $controllerName");
    return;
  }

  $controller = new $controllerName();

  if (!method_exists($controller, $action)) {
    send404("Método no encontrado: $action en $controllerName");
    return;
  }

  call_user_func_array([$controller, $action], $params);
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
