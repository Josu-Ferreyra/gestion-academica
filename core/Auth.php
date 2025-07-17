<?php
require_once 'core/DB.php';

class Auth {
  /**
   * Intenta loguear al usuario con email y contraseña (MD5).
   *
   * @param string $email Correo electrónico del usuario.
   * @param string $password Contraseña del usuario (sin encriptar).
   * @return bool Devuelve true si el inicio de sesión fue exitoso, false en caso contrario.
   */
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

  /**
   * Verifica si el usuario actual tiene alguno de los roles requeridos.
   *
   * @param array $roles Lista de roles permitidos.
   * @return bool Devuelve true si el usuario tiene uno de los roles, false en caso contrario.
   */
  public static function checkRoles(array $roles): bool {
    if (!isset($_SESSION['user'])) return false;
    return in_array($_SESSION['user']['rol'], $roles);
  }

  /**
   * Verifica si hay un usuario logueado.
   *
   * @return bool Devuelve true si hay un usuario logueado, false en caso contrario.
   */
  public static function check(): bool {
    return isset($_SESSION['user']);
  }

  /**
   * Devuelve los datos del usuario actual.
   *
   * @return array|null Devuelve un array con los datos del usuario o null si no hay usuario logueado.
   */
  public static function user() {
    return $_SESSION['user'] ?? null;
  }

  /**
   * Devuelve el rol del usuario actual.
   *
   * @return string|null Devuelve un string con el rol del usuario o null si no hay usuario logueado.
   */
  public static function getRole() {
    return $_SESSION['user']['rol'] ?? null;
  }

  /**
   * Cierra la sesión del usuario actual.
   *
   * @return void
   */
  public static function logout() {
    session_destroy();
    $_SESSION = [];
  }
}
