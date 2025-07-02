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

    // Cargar la vista correspondiente
    require_once __DIR__ . '/../views/profesor/detalle_materia.php';
  }

  public function getAllMateriaAlumnosByYear($materiaId, $year) {
    if (!is_numeric($materiaId) || $materiaId <= 0 || !is_numeric($year) || $year <= 0) {
      header('Location: /error');
      exit;
    }

    $alumnos = $this->materia->getAllMateriaAlumnosByYear($materiaId, $year);

    if ($alumnos === false) {
      header('Location: /error');
      exit;
    }

    // Cargar la vista correspondiente
    require_once __DIR__ . '/../views/profesor/tabla_alumnos.php';
  }

  public function updateNotas($id_materia, $year) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: /error');
      exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['alumnos']) || !is_array($data['alumnos'])) {
      header('Location: /error');
      exit;
    }

    $result = $this->materia->updateNotas($data['alumnos'], $id_materia, $year);

    if ($result) {
      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['success' => false]);
    }
    exit;
  }
}
