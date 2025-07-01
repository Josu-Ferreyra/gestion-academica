<?php

require_once __DIR__ . '../../models/Carrera.php';

class Alumno {
  public $id_alumno;
  public $id_carrera;
  public $fecha_ingreso;

  /**
   * Obtiene los detalles del alumno actual.
   *
   * @return array|false Retorna un array asociativo con los detalles del alumno o false en caso de error.
   */
  public function getAlumnoDetails() {
    $id_usuario = $_SESSION["user_id"] ?? null;

    if (!$id_usuario) {
      return false;
    }

    try {
      $pdo = $this->getDatabaseConnection();

      $query = '
        SELECT
          a.id_alumno,
          a.id_carrera,
          a.fecha_ingreso
        FROM alumno a
        WHERE a.id_usuario = :id
      ';
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $this->logDatabaseError($e);
      return false;
    }
  }

  /**
   * Obtiene todas las inscripciones de materias del alumno actual.
   *
   * @return array|false Retorna un array con las inscripciones o false en caso de error.
   */
  public function getAllInscripcionMateriaAlumno() {
    if (!$this->id_alumno) {
      return false;
    }

    try {
      $pdo = $this->getDatabaseConnection();

      $query = '
        SELECT
          m.id_materia,
          im.anio_academico,
          im.semestre,
          im.intentos_final,
          m.nombre AS nombre_materia,
          eim.nombre AS estado_inscripcion,
          ev.fecha AS fecha_evaluacion,
          ev.nota AS nota_evaluacion,
          tev.nombre AS tipo_evaluacion
        FROM inscripcion_materia im
        LEFT JOIN materia m ON im.id_materia = m.id_materia
        LEFT JOIN estado_inscripcion_materia eim ON im.id_estado = eim.id_estado
        LEFT JOIN evaluacion ev ON ev.id_inscripcion = im.id_inscripcion
        LEFT JOIN tipo_evaluacion tev ON ev.id_tipo = tev.id_tipo
        WHERE im.id_alumno = :id
      ';
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':id', $this->id_alumno, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $this->logDatabaseError($e);
      return false;
    }
  }

  /**
   * Obtiene una conexión a la base de datos.
   *
   * @return PDO Retorna una instancia de PDO para interactuar con la base de datos.
   */
  private function getDatabaseConnection() {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  }

  /**
   * Registra un error de base de datos en el log.
   *
   * @param PDOException $exception Excepción capturada de PDO.
   * @return void
   */
  private function logDatabaseError(PDOException $exception) {
    error_log('Error en DB: ' . $exception->getMessage());
  }
}
