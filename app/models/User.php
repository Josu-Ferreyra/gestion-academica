<?php

class User
{
  public $id_usuario;
  public $email;
  public $contrasena;

  public static function findByUsername($username)
  {
    try {
      $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $pdo->prepare('SELECT id_usuario, email, contrasena FROM usuario WHERE email = :u LIMIT 1');
      $stmt->execute(['u' => $username]);
      $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
      return $stmt->fetch();
    } catch (PDOException $e) {
      error_log('Error en DB: ' . $e->getMessage());
      echo "Error al conectar a la base de datos: " . $e->getMessage();
      return false;
    }
  }
}
