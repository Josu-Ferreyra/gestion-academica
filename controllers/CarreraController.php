<?php

require_once 'models/Carrera.php';

class CarreraController {
  /**
   * Muestra la vista para crear una nueva carrera.
   *
   * @return void
   */
  public function viewCreateCarrera() {
    include 'views/carrera/create.php';
  }

  /**
   * Crea una nueva carrera.
   *
   * @return void
   */
  public function createCarrera() {
    try {
      $id_carrera = Carrera::createCarrera([
        'nombre' => $_POST['nombre']
      ]);

      echo "Carrera creada con éxito. ID: " . $id_carrera;
    } catch (Exception $e) {
      http_response_code(500);
      echo "Error al crear la carrera: " . $e->getMessage();
      return;
    }
  }
}
