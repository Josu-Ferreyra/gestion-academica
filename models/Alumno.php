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

  /**
   * Obtiene el ID de la carrera asociada a un alumno dado su ID de usuario.
   *
   * @param int $id_usuario ID del usuario del alumno.
   * @return int ID de la carrera asociada al alumno.
   * @throws Exception Si no se encuentra el alumno o si ocurre un error en la consulta.
   */
  public static function getCarreraIdByUsuarioId($id_usuario) {
    $db = DB::getConnection();

    $stmt = $db->prepare('
      SELECT id_carrera FROM alumno WHERE id_usuario = :id_usuario
    ');

    $stmt->execute([':id_usuario' => $id_usuario]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
      return $result['id_carrera'];
    } else {
      throw new Exception('No se encontró el alumno con el ID de usuario proporcionado.');
    }
  }

  /**
   * Obtiene el ID del alumno dado su ID de usuario.
   *
   * @param int $id_usuario ID del usuario del alumno.
   * @return int ID del alumno asociado al usuario.
   * @throws Exception Si no se encuentra el alumno o si ocurre un error en la consulta.
   */
  public static function getAlumnoIdByUsuarioId($id_usuario) {
    $db = DB::getConnection();

    $stmt = $db->prepare('
      SELECT id_alumno FROM alumno WHERE id_usuario = :id_usuario
    ');

    $stmt->execute([':id_usuario' => $id_usuario]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
      return $result['id_alumno'];
    } else {
      throw new Exception('No se encontró el alumno con el ID de usuario proporcionado.');
    }
  }
}
