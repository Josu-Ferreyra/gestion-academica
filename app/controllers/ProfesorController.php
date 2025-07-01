<?php
require_once __DIR__ . '../../models/Profesor.php';

class ProfesorController {
  private $profesor;

  /**
   * Constructor de la clase ProfesorController.
   * Verifica que el usuario esté autenticado y tenga el rol de profesor.
   */
  public function __construct() {
    if (empty($_SESSION['user_id']) || $_SESSION['rol'] !== 'profesor') {
      header('Location: /gestion-academica');
      exit;
    }

    $this->profesor = new Profesor();
  }

  /**
   * Controlador para la página principal del profesor.
   * Obtiene el ID del profesor y las materias asociadas.
   *
   * @return void
   */
  public function home() {
    $profesorId = $this->profesor->getProfesorId();
    if (!$profesorId) {
      // Manejo de error si no se obtiene el ID del profesor.
      header('Location: /error');
      exit;
    }

    $profesorMaterias = $this->profesor->getProfesorMaterias();
    if ($profesorMaterias === false) {
      // Manejo de error si no se obtienen las materias del profesor.
      header('Location: /error');
      exit;
    }

    require_once __DIR__ . '/../views/profesor/home.php';
  }
}
