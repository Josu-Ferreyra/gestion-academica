<?php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {

  public function showLogin() {
    require_once __DIR__ . '/../views/auth/login.php';
  }

  public function login() {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_hash = md5($password);

    $user = Usuario::findByEmail($email);

    if ($user && ($password_hash === $user->password)) {
      $_SESSION['user_id'] = $user->user_id;
      $_SESSION['rol'] = $user->rol;
      header('Location: ./?url=auth/dashboard');
      exit;
    }

    $error = 'Credenciales inválidas';
    require_once __DIR__ . '/../views/auth/login.php';
  }

  public function dashboard() {
    if (empty($_SESSION['user_id'])) {
      header('Location: /gestion-academica');
      exit;
    }
    require_once __DIR__ . '/../views/auth/dashboard.php';
  }

  public function logout() {
    session_destroy();
    header('Location: /gestion-academica');
    exit;
  }
}
