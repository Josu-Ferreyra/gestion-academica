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

  /**
   * Obtiene el estado académico de un alumno.
   *
   * @param int $id_alumno ID del alumno.
   * @return array Un array asociativo con el estado académico del alumno.
   * @throws Exception Si el ID del alumno está vacío o no se encuentra.
   */
  public static function getEstadoAcademicoByAlumno($id_alumno) {
    if (empty($id_alumno)) {
      throw new Exception("ID de alumno no proporcionado.");
    }

    $db = DB::getConnection();

    $stmt = $db->prepare("
      SELECT
        view.anio_academico,
        view.estado_inscripcion,
        parcial_1,
        parcial_2,
        recuperatorio_1,
        recuperatorio_2,
        nota_final,
        m.nombre AS nombre_materia,
        m.anio AS anio,
        m.semestre AS semestre
      FROM v_notas_por_inscripcion AS view
      INNER JOIN materia m ON view.id_materia = m.id_materia
      WHERE id_alumno = :id_alumno
    ");

    $stmt->bindParam(':id_alumno', $id_alumno);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Obtiene los detalles de las inscripciones a una materia específica.
   *
   * @param int $id_materia ID de la materia.
   * @return array Un array asociativo con los detalles de las inscripciones.
   * @throws Exception Si el ID de la materia está vacío o no se encuentra.
   */
  public static function getInscripcionesMateriaDetails($id_materia) {
    if (empty($id_materia)) {
      throw new Exception("ID de materia no proporcionado.");
    }

    $db = DB::getConnection();

    $stmt = $db->prepare("
      SELECT
        id_inscripcion,
        id_alumno,
        nombre_alumno,
        apellido_alumno,
        anio_academico,
        parcial_1,
        parcial_2,
        recuperatorio_1,
        recuperatorio_2,
        nota_final,
        estado_inscripcion
      FROM v_notas_por_inscripcion
      WHERE id_materia = :id_materia
      ORDER BY id_alumno
    ");

    $stmt->bindParam(':id_materia', $id_materia);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /** Actualiza las notas de los alumnos en una materia específica.
   * Recibe un JSON con los datos de los alumnos, ID de materia y año académico.
   *
   * @param array $alumnos Array de alumnos con sus notas.
   * @param int $id_materia ID de la materia.
   * @param int $year Año académico.
   * @return array Resultado de la actualización.
   * @throws Exception Si falta información necesaria.
   */
  public static function updateNotas($alumnos, $id_materia, $year) {
    if (empty($alumnos) || empty($id_materia) || empty($year)) {
      throw new Exception("No se proporcionó la información necesaria");
    }

    $db = DB::getConnection();
    $jsonAlumnos = json_encode($alumnos);

    $stmt = $db->prepare("CALL actualizar_notas_alumnos(:alumnos, :id_materia, :year)");

    $stmt->bindParam(':alumnos', $jsonAlumnos);
    $stmt->bindParam(':id_materia', $id_materia);
    $stmt->bindParam(':year', $year);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
