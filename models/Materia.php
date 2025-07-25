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
}
