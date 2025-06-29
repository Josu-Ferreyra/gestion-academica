<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{
  public function showLogin()
  {
    require __DIR__ . '/../views/auth/login.php';
  }

  public function login()
  {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_hash = md5($password);

    $user = User::findByUsername($username);

    if ($user && ($password_hash === $user->contrasena)) {
      $_SESSION['user_id'] = $user->id_usuario;
      header('Location: ./?url=auth/dashboard');
      exit;
    }

    $error = 'Credenciales inválidas';
    require __DIR__ . '/../views/auth/login.php';
  }

  public function dashboard()
  {
    if (empty($_SESSION['user_id'])) {
      header('Location: /gestion-academica');
      exit;
    }
    require __DIR__ . '/../views/auth/dashboard.php';
  }

  public function logout()
  {
    session_destroy();
    header('Location: /gestion-academica');
    exit;
  }
}
