<?php

class Carrera {
  /**
   * Obtiene todas las carreras disponibles.
   * @return array Lista de carreras con sus detalles.
   */
  public static function getAllCarreras() {
    $db = DB::getConnection();
    $stmt = $db->prepare('SELECT * FROM carrera');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
