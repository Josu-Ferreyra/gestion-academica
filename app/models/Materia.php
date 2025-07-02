<?php

class Materia {
  /**
   * Obtiene los alumnos inscritos en una materia específica.
   *
   * @param int $materiaId ID de la materia.
   * @return array|false Retorna un array con los alumnos inscritos o false en caso de error.
   */
  public function getMateriaAlumnos($materiaId) {
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
