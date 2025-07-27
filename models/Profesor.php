<?php

class Profesor {
  /**
   * Obtiene todos los profesores de la base de datos.
   *
   * @return array Un array asociativo con los profesores, donde cada elemento contiene 'id_profesor', 'nombre', 'titulo_academico', 'especialidad' y 'materias'.
   */
  public static function getAllProfesores() {
    $db = DB::getConnection();

    $stmt = $db->prepare("
      SELECT
        p.id_profesor,
        u.nombre,
        u.apellido,
        p.titulo_academico,
        p.especialidad,
        GROUP_CONCAT(m.nombre SEPARATOR ', ') AS materias
      FROM profesor p
      INNER JOIN usuario u ON p.id_usuario = u.id_usuario
      LEFT JOIN profesor_materia pm ON p.id_profesor = pm.id_profesor
      LEFT JOIN materia m ON pm.id_materia = m.id_materia
      GROUP BY p.id_profesor, u.nombre, p.titulo_academico, p.especialidad
    ");
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Crea un nuevo profesor en la base de datos.
   *
   * @param array $data Datos del profesor, incluyendo id_usuario, titulo_academico, especialidad y materias.
   * @return int ID del nuevo profesor creado.
   * @throws Exception Si faltan datos requeridos o si ocurre un error al insertar en la base de datos.
   */
  public static function createProfesor($data) {
    $id_usuario = $data['id_usuario'] ?? null;
    $titulo_academico = $data['titulo_academico'] ?? null;
    $especialidad = $data['especialidad'] ?? null;
    $materias = $data['materias'] ?? [];

    if (!$id_usuario || empty($materias)) {
      throw new Exception("Faltan datos requeridos para crear el profesor.");
    }

    $db = DB::getConnection();

    $stmt = $db->prepare("
      INSERT INTO profesor (id_usuario, titulo_academico, especialidad) VALUES (:id_usuario, :titulo_academico, :especialidad)
    ");
    $stmt->bindParam(':id_usuario', $data['id_usuario']);
    $stmt->bindParam(':titulo_academico', $data['titulo_academico']);
    $stmt->bindParam(':especialidad', $data['especialidad']);

    $stmt->execute();

    $profesorId = $db->lastInsertId();

    if (!$profesorId) {
      throw new Exception("Error al crear el profesor.");
    }

    if (isset($data['materias']) && is_array($data['materias'])) {
      foreach ($data['materias'] as $materiaId) {
        $stmtMateria = $db->prepare("INSERT INTO profesor_materia (id_profesor, id_materia) VALUES (:id_profesor, :id_materia)");
        $stmtMateria->bindParam(':id_profesor', $profesorId);
        $stmtMateria->bindParam(':id_materia', $materiaId);
        $stmtMateria->execute();
      }
    }

    return $profesorId;
  }

  /**
   * Obtiene el ID del profesor asociado a un usuario.
   *
   * @param int $id_usuario ID del usuario.
   * @return int|null ID del profesor o null si no se encuentra.
   * @throws Exception Si el ID del usuario no se proporciona.
   */
  public static function getProfesorIdByUsuario($id_usuario) {
    if (empty($id_usuario)) {
      throw new Exception("ID de usuario no proporcionado.");
    }

    $db = DB::getConnection();

    $stmt = $db->prepare("SELECT id_profesor FROM profesor WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    return $stmt->fetchColumn();
  }
}
