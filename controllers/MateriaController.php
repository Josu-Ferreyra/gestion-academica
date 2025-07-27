<?php

require_once 'models/Materia.php';
require_once 'models/Carrera.php';
require_once 'models/Profesor.php';

class MateriaController {

  /**
   * Muestra la vista para crear una nueva materia.
   *
   * @return void
   */
  public function viewCreateMateria() {
    $profesores = Profesor::getAllProfesores();
    $carreras = Carrera::getAllCarreras();

    require_once 'views/materia/create.php';
  }

  /**
   * Crea una nueva materia.
   *
   * @return void
   */
  public function createMateria() {
    try {
      $id_materia = Materia::createMateria([
        'nombre' => $_POST['nombre'] ?? null,
        'anio' => $_POST['anio'] ?? null,
        'semestre' => $_POST['semestre'] ?? null,
        'id_carrera' => $_POST['id_carrera'] ?? null,
        'id_profesor' => $_POST['id_profesor'] ?? null
      ]);

      echo "Materia creada con éxito. ID: $id_materia";
    } catch (Exception $e) {
      $error = $e->getMessage();
      require_once 'views/materia/create.php';
    }
  }
}
