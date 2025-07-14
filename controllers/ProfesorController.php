<?php

require_once 'core/Auth.php';

class ProfesorController {
  /**
   * Muestra la vista principal para los profesores.
   * Verifica que el usuario autenticado tenga el rol de "profesor".
   * Si no es un profesor o no está autenticado, devuelve un código de respuesta 403.
   *
   * @return void
   */
  public function index() {
    $user = Auth::user();

    if (!$user || $user['rol'] !== 'profesor') {
      http_response_code(403);
      echo "403 - Acceso no autorizado";
      return;
    }

    include 'views/profesor/index.php';
  }
}
