<?php

class Usuario {
  /**
   * Crea un nuevo usuario en la base de datos.
   *
   * @param array $data Datos del usuario a crear, debe contener 'nombre', 'apellido', 'contrasena', 'email', 'id_rol'.
   * @return int ID del nuevo usuario creado.
   * @throws Exception Si falta algún campo obligatorio o si se intenta crear un usuario con rol de administrador.
   */
  public static function createUsuario($data) {
    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    $contrasena = $data['contrasena'];
    $email = $data['email'];
    $id_rol = $data['id_rol'];
    $direccion = isset($data['direccion']) ? $data['direccion'] : null;
    $telefono = isset($data['telefono']) ? $data['telefono'] : null;
    $activo = isset($data['activo']) ? $data['activo'] : 1;

    if (empty($nombre) || empty($apellido) || empty($contrasena) || empty($email) || empty($id_rol)) {
      throw new Exception('Hay campos obligatorios sin settear.');
    }

    if ($id_rol === 1) {
      throw new Exception('No se puede crear un usuario con rol de administrador.');
    }

    $db = DB::getConnection();

    $stmt = $db->prepare('
      INSERT INTO usuario (nombre, apellido, email, contrasena, id_rol, direccion, telefono, activo)
      VALUES (:nombre, :apellido, :email, MD5(:contrasena), :rol, :direccion, :telefono, :activo)
    ');

    $stmt->execute([
      ':nombre' => $nombre,
      ':apellido' => $apellido,
      ':email' => $email,
      ':contrasena' => $contrasena,
      ':rol' => $id_rol,
      ':direccion' => $direccion,
      ':telefono' => $telefono,
      ':activo' => $activo
    ]);

    return $db->lastInsertId();
  }
}
