<?php

class Materia {
  /**
   * Obtiene los alumnos inscritos en una materia específica.
   *
   * @param int $materiaId ID de la materia.
   * @return array|false Retorna un array con los alumnos inscritos o false en caso de error.
   */
  public function getAllMateriaAlumnos($materiaId) {
    if (!$materiaId) {
      return false;
    }

    try {
      $pdo = $this->getDatabaseConnection();

      $query = '
        SELECT *
        FROM v_notas_por_inscripcion
        WHERE id_materia = :id
      ';
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':id', $materiaId, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $this->logDatabaseError($e);
      return false;
    }
  }

  public function getAllMateriaAlumnosByYear($materiaId, $year) {
    if (!$materiaId || !$year) {
      return false;
    }

    try {
      $pdo = $this->getDatabaseConnection();

      $query = '
        SELECT *
        FROM v_notas_por_inscripcion
        WHERE id_materia = :id AND anio_academico = :year
      ';
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':id', $materiaId, PDO::PARAM_INT);
      $stmt->bindParam(':year', $year, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $this->logDatabaseError($e);
      return false;
    }
  }

  public function getMateriaAlumno($alumnoId) {
    if (!$alumnoId) {
      return false;
    }

    try {
      $pdo = $this->getDatabaseConnection();

      $query = '
        SELECT *
        FROM v_notas_por_inscripcion
        WHERE id_alumno = :alumnoId
      ';
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':alumnoId', $alumnoId, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $this->logDatabaseError($e);
      return false;
    }
  }

  public function inscribirAlumnoMateria($alumnoId, $materiaId): array {
    if (!$alumnoId || !$materiaId) {
      return ['success' => false, 'mensaje' => 'ID de alumno o materia inválido', 'alumnoId' => $alumnoId, 'materiaId' => $materiaId];
    }
    try {
      $pdo = $this->getDatabaseConnection();
      $query = '
      SELECT inscribir_alumno_materia(:id_alumno, :id_materia) AS mensaje;
    ';
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':id_alumno', $alumnoId, PDO::PARAM_INT);
      $stmt->bindParam(':id_materia', $materiaId, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return [
        'success' => $result['mensaje'] === 'Inscripción realizada con éxito.',
        'mensaje' => $result['mensaje']
      ];
    } catch (PDOException $e) {
      $this->logDatabaseError($e);
      return ['success' => false, 'mensaje' => 'Error en la base de datos'];
    }
  }

  public function updateNotas($alumnos, $materiaId, $year) {
    if (empty($alumnos) || !is_array($alumnos)) {
      return false;
    }

    try {
      $pdo = $this->getDatabaseConnection();

      $query = '
          CALL actualizar_notas_alumnos(:alumnos, :materiaId, :year)
        ';
      $stmt = $pdo->prepare($query);
      $jsonAlumnos = json_encode($alumnos);
      $stmt->bindParam(':alumnos', $jsonAlumnos, PDO::PARAM_STR);
      $stmt->bindParam(':materiaId', $materiaId, PDO::PARAM_INT);
      $stmt->bindParam(':year', $year, PDO::PARAM_INT);
      $stmt->execute();
      return true;
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
    try {
      $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $pdo;
    } catch (PDOException $e) {
      $this->logDatabaseError($e);
      throw new Exception('No se pudo establecer la conexión a la base de datos.');
    }
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
