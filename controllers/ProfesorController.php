<?php

require_once 'core/Auth.php';

class ProfesorController {
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
