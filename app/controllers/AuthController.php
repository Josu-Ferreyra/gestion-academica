<?php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {

  /**
   * Maneja el proceso de inicio de sesión.
   */
  public function login() {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
      $error = 'Por favor, complete todos los campos.';
      require_once __DIR__ . '/../views/auth/login.php';
      return;
    }

    $password_hash = md5($password);

    $user = Usuario::findByEmail($email);

    if ($user && ($password_hash === $user->password)) {
      $_SESSION['user_id'] = $user->user_id;
      $_SESSION['rol'] = $user->rol;

      header('Location: ./?url=auth/loadView');
      exit;
    }

    $error = 'Credenciales inválidas';
    require_once __DIR__ . '/../views/auth/login.php';
  }

  /**
   * Carga la vista principal según el rol del usuario.
   */
  public function loadView() {
    if (empty($_SESSION['user_id']) || empty($_SESSION['rol'])) {
      require_once __DIR__ . '/../views/auth/login.php';
      exit;
    }

    switch ($_SESSION['rol']) {
      case 'alumno':
        header('Location: ./?url=alumno/home');
        exit;
      case 'profesor':
        header('Location: ./?url=profesor/home');
        exit;
      default:
        $this->logout();
        exit;
    }
  }

  /**
   * Maneja el cierre de sesión del usuario.
   */
  public function logout() {
    session_destroy();
    header('Location: /gestion-academica');
    exit;
  }
}
