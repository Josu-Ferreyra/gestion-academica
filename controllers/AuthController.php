<?php
// controllers/AuthController.php

require_once 'core/Auth.php';

class AuthController {
  // Muestra el formulario de login
  public function login() {
    if (Auth::check()) {
      header('Location: /');
      exit;
    }

    include 'views/login.php';
  }

  // Procesa el formulario de login
  public function doLogin() {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (Auth::attempt($email, $password)) {
      header('Location: /');
    } else {
      $error = "Credenciales incorrectas o usuario inactivo.";
      include 'views/login.php';
    }
  }

  // Cierra sesión
  public function logout() {
    Auth::logout();
    header('Location: /login');
    exit;
  }
}
