<?php
// core/Auth.php

require_once 'core/DB.php';

class Auth {
  // Intenta loguear al usuario con email y contraseña (MD5)
  public static function attempt($email, $password) {
    $db = DB::getConnection();

    $stmt = $db->prepare('
            SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.id_rol, r.nombre AS rol
            FROM usuario u
            JOIN rol_usuario r ON u.id_rol = r.id_rol
            WHERE u.email = :email AND u.contrasena = MD5(:password) AND u.activo = 1
            LIMIT 1
        ');

    $stmt->execute([
      ':email' => $email,
      ':password' => $password
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      $_SESSION['user'] = $user;
      return true;
    }

    return false;
  }

  // Verifica si el usuario actual tiene alguno de los roles requeridos
  public static function checkRoles(array $roles): bool {
    if (!isset($_SESSION['user'])) return false;
    return in_array($_SESSION['user']['rol'], $roles);
  }

  // Devuelve true si hay usuario logueado
  public static function check(): bool {
    return isset($_SESSION['user']);
  }

  // Devuelve los datos del usuario actual
  public static function user() {
    return $_SESSION['user'] ?? null;
  }

  // Cierra la sesión
  public static function logout() {
    session_destroy();
    $_SESSION = [];
  }
}
