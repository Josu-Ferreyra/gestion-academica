<?php

class User {
  public $user_id;
  public $email;
  public $password;
  public $rol;

  public static function findByEmail($email) {
    try {
      $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $pdo->prepare(
        'SELECT
          id_usuario as user_id,
          email,
          contrasena AS password,
          ru.nombre AS rol
        FROM usuario u
        LEFT JOIN rol_usuario ru
        ON u.id_rol = ru.id_rol
        WHERE email = :u
        LIMIT 1;'
      );
      $stmt->bindParam(':u', $email, PDO::PARAM_STR);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
      return $stmt->fetch();
    } catch (PDOException $e) {
      error_log('Error en DB: ' . $e->getMessage());
      return false;
    }
  }
}
