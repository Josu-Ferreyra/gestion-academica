<?php

class Alumno {
  /**
   * Crea un nuevo alumno en la base de datos.
   *
   * @param array $data Datos del alumno a crear, debe contener 'id_carrera', 'id_usuario' y 'fecha_ingreso'.
   * @return int ID del nuevo alumno creado.
   * @throws Exception Si falta algún campo obligatorio o si ocurre un error al insertar.
   */
  public static function createAlumno($data) {
    $id_carrera = $data['id_carrera'];
    $id_usuario = $data['id_usuario'];
    $fecha_ingreso = $data['fecha_ingreso'];

    if (empty($id_carrera) || empty($id_usuario) || empty($fecha_ingreso)) {
      throw new Exception('Todos los campos son obligatorios.');
    }

    $db = DB::getConnection();

    $stmt = $db->prepare('
      INSERT INTO alumno (id_carrera, id_usuario, fecha_ingreso)
      VALUES (:id_carrera, :id_usuario, :fecha_ingreso)
    ');

    $stmt->execute([
      ':id_carrera' => $id_carrera,
      ':id_usuario' => $id_usuario,
      ':fecha_ingreso' => $fecha_ingreso
    ]);

    return $db->lastInsertId();
  }
}
