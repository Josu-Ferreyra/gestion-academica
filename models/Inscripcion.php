<?php

class Inscripcion {
  /**
   * Obtiene todas las inscripciones de un alumno dado su ID.
   *
   * @param int $id_alumno ID del alumno.
   * @return array Un array asociativo con las inscripciones del alumno.
   * @throws Exception Si el ID del alumno está vacío o no se encuentra.
   */
  public static function getAllInscripcionesByAlumno($id_alumno) {
    if (empty($id_alumno)) {
      throw new Exception("ID de alumno no proporcionado.");
    }

    $db = DB::getConnection();

    $stmt = $db->prepare("
      SELECT
        i.id_inscripcion,
        i.anio_academico,
        i.semestre,
        i.id_materia,
        m.nombre AS nombre_materia,
        e.nombre AS estado
      FROM inscripcion_materia i
      INNER JOIN materia m ON i.id_materia = m.id_materia
      INNER JOIN estado_inscripcion_materia e ON i.id_estado = e.id_estado
      WHERE i.id_alumno = :id_alumno
    ");

    $stmt->bindParam(':id_alumno', $id_alumno);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Inscribe un alumno en una materia.
   *
   * @param int $id_alumno ID del alumno.
   * @param int $id_materia ID de la materia.
   * @throws Exception Si falta algún ID.
   */
  public static function enrol($id_alumno, $id_materia) {
    if (empty($id_alumno) || empty($id_materia)) {
      throw new Exception("ID de alumno o materia no proporcionado.");
    }

    $db = DB::getConnection();

    $stmt = $db->prepare("
      INSERT INTO inscripcion_materia (id_alumno, id_materia, anio_academico, semestre)
      VALUES (:id_alumno, :id_materia, YEAR(CURDATE()), CEILING(QUARTER(CURDATE())/2))
    ");
    $stmt->bindParam(':id_alumno', $id_alumno);
    $stmt->bindParam(':id_materia', $id_materia);
    $stmt->execute();
  }
}
