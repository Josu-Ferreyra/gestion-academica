<?php

require_once 'models/Alumno.php';
require_once 'models/Materia.php';
require_once 'models/Inscripcion.php';

class InscripcionController {

  /** Muestra las materias disponibles para inscribirse.
   * Solo muestra materias del semestre actual y aquellas que no están inscritas.
   * Si la materia ya está inscrita con estado 'recursar', también se muestra.
   */
  public function viewenrolMateria() {
    $id_usuario = $_SESSION['user']['id_usuario'] ?? null;
    $id_alumno = Alumno::getAlumnoIdByUsuarioId($id_usuario);
    $id_carrera = Alumno::getCarreraIdByUsuarioId($id_usuario);

    $materias = Materia::getMateriasByCarrera($id_carrera);
    $inscripciones = Inscripcion::getAllInscripcionesByAlumno($id_alumno);

    $materias_disponibles = [];
    foreach ($materias as $materia) {
      $inscripta = false;

      // Caso de recursar: si la materia ya está inscrita y el estado es 'recursar', se muestra
      foreach ($inscripciones as $inscripcion) {
        if ($inscripcion['id_materia'] == $materia['id_materia']) {
          if ($inscripcion['estado'] == 'recursar') {
            $materias_disponibles[] = $materia;
          }
          $inscripta = true;
          break;
        }
      }

      // Solo mostrar materias del semestre actual.
      if ($materia['semestre'] != date('n') % 2 + 1) {
        continue;
      }

      // Si el alumno no está inscripto y la materia es del semestre actual se muestra.
      if (!$inscripta) {
        $materias_disponibles[] = $materia;
      }
    }

    require_once 'views/alumno/enrol.php';
  }

  /** Inscribe al alumno en una materia.
   * Recibe el ID de la materia a través de una solicitud JSON.
   * Valida que el ID de materia sea proporcionado y maneja errores.
   */
  public function enrolMateria() {
    $id_usuario = $_SESSION['user']['id_usuario'] ?? null;
    $id_alumno = Alumno::getAlumnoIdByUsuarioId($id_usuario);

    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);
    $id_materia = $data["id_materia"];

    if (!$id_materia) {
      http_response_code(400);
      echo json_encode(['message' => 'ID de materia no proporcionado']);
      return;
    }

    try {
      Inscripcion::enrol($id_alumno, $id_materia);
      echo json_encode(['message' => 'Inscripción exitosa']);
    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['message' => 'Error al inscribirse: ' . $e->getMessage()]);
    }
  }
}
