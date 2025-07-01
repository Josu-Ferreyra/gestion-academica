<?php
class Carrera {

  /**
   * Obtiene todas las materias asociadas a una carrera específica.
   *
   * @param int $id_carrera ID de la carrera para filtrar las materias.
   * @return array|false Retorna un array con las materias de la carrera o false en caso de error.
   */
  public function getAllMateriaCarrera($id_carrera) {
    if (!$id_carrera) {
      return false;
    }

    try {
      $pdo = $this->getDatabaseConnection();

      $query = '
        SELECT
          m.id_materia,
          m.nombre AS nombre_materia,
          m.anio AS anio_materia,
          m.semestre AS semestre_materia
        FROM carrera c
        LEFT JOIN materia m ON m.id_carrera = :id
      ';
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':id', $id_carrera, PDO::PARAM_INT);
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
