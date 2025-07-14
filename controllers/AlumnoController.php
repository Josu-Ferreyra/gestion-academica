<?php
require_once 'core/Auth.php';

class AlumnoController {
  public function index() {
    $user = Auth::user();

    // Verificamos que sea alumno
    if (!$user || $user['rol'] !== 'alumno') {
      http_response_code(403);
      echo "403 - Acceso no autorizado";
      return;
    }

    include 'views/alumno.php';
  }
}
