<?php

require_once 'core/DB.php';

class Materia {
  /**
   * Obtiene todas las materias de la base de datos.
   *
   * @return array Un array asociativo con las materias, donde cada elemento contiene 'id_materia' y 'nombre'.
   */
  public static function getAllMaterias() {
    $db = DB::getConnection();

    $stmt = $db->prepare("
      SELECT
        m.id_materia,
        m.nombre,
        m.anio,
        m.semestre,
        m.id_carrera,
        c.nombre AS carrera
      FROM materia m
      INNER JOIN carrera c
      ON m.id_carrera = c.id_carrera
    ");
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Crea una nueva materia en la base de datos.
   *
   * @param array $data Datos de la materia, incluyendo 'nombre', 'anio', 'semestre', 'id_carrera' y 'id_profesor'.
   * @return int ID de la materia creada.
   * @throws Exception Si faltan datos requeridos.
   */
  public static function createMateria($data) {
    if (!isset($data['nombre']) || !isset($data['anio']) || !isset($data['semestre']) || !isset($data['id_carrera'])) {
      throw new Exception("Faltan datos requeridos para crear la materia.");
    }

    $db = DB::getConnection();

    $stmt = $db->prepare("
      INSERT INTO materia (nombre, anio, semestre, id_carrera)
      VALUES (:nombre, :anio, :semestre, :id_carrera)
    ");

    $stmt->bindParam(':nombre', $data['nombre']);
    $stmt->bindParam(':anio', $data['anio']);
    $stmt->bindParam(':semestre', $data['semestre']);
    $stmt->bindParam(':id_carrera', $data['id_carrera']);

    $stmt->execute();

    $id_materia = $db->lastInsertId();

    if ($data['id_profesor'] != null && $data['id_profesor'] != '') {
      $id_profesor = $data['id_profesor'];

      $stmt = $db->prepare("
        INSERT INTO profesor_materia (id_materia, id_profesor)
        VALUES (:id_materia, :id_profesor)
      ");
      $stmt->bindParam(':id_materia', $id_materia);
      $stmt->bindParam(':id_profesor', $id_profesor);
      $stmt->execute();
    }

    return $id_materia;
  }

  /**
   * Obtiene las materias asociadas a una carrera específica.
   *
   * @param int $id_carrera ID de la carrera.
   * @return array Un array asociativo con las materias de la carrera.
   * @throws Exception Si el ID de carrera está vacío o no se encuentra.
   */
  public static function getMateriasByCarrera($id_carrera) {
    if (empty($id_carrera)) {
      throw new Exception("El ID de carrera no puede estar vacío.");
    }

    $db = DB::getConnection();

    $stmt = $db->prepare("
      SELECT
        m.id_materia,
        m.nombre,
        m.anio,
        m.semestre
      FROM materia m
      WHERE m.id_carrera = :id_carrera
    ");
    $stmt->bindParam(':id_carrera', $id_carrera);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
