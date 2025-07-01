<?php

require_once __DIR__ . '../../models/Alumno.php';
require_once __DIR__ . '../../models/Carrera.php';

class AlumnoController {
  private $alumno;

  /**
   * Constructor de la clase AlumnoController.
   * Verifica que el usuario esté autenticado y tenga el rol de alumno.
   */
  public function __construct() {
    if (empty($_SESSION['user_id']) || $_SESSION['rol'] !== 'alumno') {
      header('Location: /gestion-academica');
      exit;
    }

    $this->alumno = new Alumno();
  }

  /**
   * Controlador para la página principal del alumno.
   * Obtiene los detalles del alumno, sus inscripciones y las materias de su carrera.
   *
   * @return void
   */
  public function home() {
    $alumnoDetails = $this->alumno->getAlumnoDetails();
    if (!$alumnoDetails) {
      // Manejo de error si no se obtienen detalles del alumno.
      header('Location: /error');
      exit;
    }

    $this->alumno->id_alumno = $alumnoDetails['id_alumno'];

    $inscripciones = $this->alumno->getAllInscripcionMateriaAlumno();
    $carrera = new Carrera();
    $materias = $carrera->getAllMateriaCarrera($alumnoDetails['id_carrera']);

    $materiasAlumno = $this->organizarMateriasAlumno($materias, $inscripciones);

    require_once __DIR__ . '/../views/alumno/home.php';
  }

  /**
   * Organiza las materias del alumno junto con sus inscripciones.
   *
   * @param array $materias Lista de materias de la carrera.
   * @param array $inscripciones Lista de inscripciones del alumno.
   * @return array Retorna un array organizado con las materias y sus inscripciones.
   */
  private function organizarMateriasAlumno(array $materias, array $inscripciones): array {
    $materiasAlumno = [];

    foreach ($materias as $materia) {
      $materiasAlumno[$materia['id_materia']] = [
        'nombre' => $materia['nombre_materia'],
        'anio' => $materia['anio_materia'],
        'semestre' => $materia['semestre_materia'],
        'inscripciones' => []
      ];
    }

    foreach ($inscripciones as $inscripcion) {
      $materiasAlumno[$inscripcion['id_materia']]['inscripciones'][] = [
        'anio_academico' => $inscripcion['anio_academico'],
        'semestre' => $inscripcion['semestre'],
        'intentos_final' => $inscripcion['intentos_final'],
        'nombre_materia' => $inscripcion['nombre_materia'],
        'estado_inscripcion' => $inscripcion['estado_inscripcion'],
        'fecha_evaluacion' => $inscripcion['fecha_evaluacion'],
        'nota_evaluacion' => $inscripcion['nota_evaluacion']
      ];
    }

    return $materiasAlumno;
  }
}
