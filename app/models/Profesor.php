<?php

class Profesor {
  public $id_profesor;

  /**
   * Constructor de la clase Profesor.
   * Obtiene el ID del profesor basado en la sesión actual.
   */
  public function __construct() {
    $this->getProfesorId();
  }

  /**
   * Obtiene el ID del profesor basado en el usuario autenticado.
   *
   * @return int|false Retorna el ID del profesor o false en caso de error.
   */
  public function getProfesorId() {
    if (empty($_SESSION['user_id']) || $_SESSION['rol'] !== 'profesor') {
      header('Location: /gestion-academica');
      exit;
    }

    $id_usuario = $_SESSION["user_id"] ?? null;

    try {
      $pdo = $this->getDatabaseConnection();

      $query = '
        SELECT
          p.id_profesor
        FROM profesor p
        WHERE p.id_usuario = :id
      ';
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
      $stmt->execute();

      $profesor = $stmt->fetch(PDO::FETCH_ASSOC);
      $this->id_profesor = $profesor['id_profesor'] ?? null;

      return $this->id_profesor;
    } catch (PDOException $e) {
      $this->logDatabaseError($e);
      return false;
    }
  }

  /**
   * Obtiene las materias asociadas al profesor.
   *
   * @return array|false Retorna un array con las materias del profesor o false en caso de error.
   */
  public function getProfesorMaterias() {
    if (!$this->id_profesor) {
      return false;
    }

    $id_profesor = $this->id_profesor;

    try {
      $pdo = $this->getDatabaseConnection();

      $query = '
        SELECT
          pm.id_profesor,
          pm.id_materia,
          m.nombre AS nombre_materia
        FROM profesor_materia pm
        LEFT JOIN materia m ON pm.id_materia = m.id_materia
        WHERE pm.id_profesor = :id
      ';
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':id', $id_profesor, PDO::PARAM_INT);
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
