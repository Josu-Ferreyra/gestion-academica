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

  /**
   * Crea una nueva carrera en la base de datos.
   * @param array $data Datos de la carrera, incluyendo 'nombre'.
   * @return int ID de la carrera creada.
   * @throws Exception Si falta el nombre de la carrera.
   */
  public static function createCarrera($data) {
    $nombre = $data['nombre'] ?? '';

    if (empty($nombre)) {
      throw new Exception('El nombre de la carrera es obligatorio.');
    }

    $db = DB::getConnection();

    $stmt = $db->prepare('INSERT INTO carrera (nombre) VALUES (:nombre)');
    $stmt->bindParam(':nombre', $nombre);
    $stmt->execute();

    $id_carrera = $db->lastInsertId();

    return  $id_carrera;
  }
}
