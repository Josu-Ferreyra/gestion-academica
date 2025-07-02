<?php

class MateriaController {
  private $materia;

  /**
   * Constructor de la clase MateriaController.
   * Inicializa el modelo Materia.
   */
  public function __construct() {
    require_once __DIR__ . '../../models/Materia.php';
    $this->materia = new Materia();
  }

  /**
   * Controlador para obtener los alumnos inscritos en una materia.
   *
   * @param int $materiaId ID de la materia.
   * @return void
   */
  public function detalleMateria($materiaId) {
    if (!is_numeric($materiaId) || $materiaId <= 0) {
      header('Location: /error');
      exit;
    }

    $alumnos = $this->materia->getMateriaAlumnos($materiaId);

    if ($alumnos === false) {
      header('Location: /error');
      exit;
    }

    // Cargar la vista correspondiente
    require_once __DIR__ . '/../views/profesor/detalle_materia.php';
  }
}
