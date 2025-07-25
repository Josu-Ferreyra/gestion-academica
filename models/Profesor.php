<?php

class Profesor {
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
}
