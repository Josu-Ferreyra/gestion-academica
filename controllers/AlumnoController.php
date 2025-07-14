<?php
require_once 'core/Auth.php';

class AlumnoController {
  /**
   * Muestra la vista principal para los alumnos.
   * Verifica que el usuario autenticado tenga el rol de "alumno".
   * Si no es un alumno o no está autenticado, devuelve un código de respuesta 403.
   *
   * @return void
   */
  public function index() {
    $user = Auth::user();

    // Verificamos que sea alumno
    if (!$user || $user['rol'] !== 'alumno') {
      http_response_code(403);
      echo "403 - Acceso no autorizado";
      return;
    }

    include 'views/alumno/index.php';
  }
}
