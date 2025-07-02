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
