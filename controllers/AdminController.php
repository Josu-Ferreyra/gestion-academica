<?php

require_once 'core/Auth.php';

class AdminController {
  /**
   * Muestra la vista principal para los administradores.
   * Verifica que el usuario autenticado tenga el rol de "admin".
   * Si no es un administrador o no está autenticado, devuelve un código de respuesta 403.
   *
   * @return void
   */
  public function index() {
    $user = Auth::user();

    if (!$user || $user['rol'] !== 'admin') {
      http_response_code(403);
      echo "403 - Acceso no autorizado";
      return;
    }

    include 'views/admin/index.php';
  }
}
