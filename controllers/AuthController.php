<?php
require_once 'core/Auth.php';

class AuthController {
  /**
   * Muestra el formulario de login.
   * Si el usuario ya está autenticado, redirige a la página principal.
   *
   * @return void
   */
  public function login() {
    if (Auth::check()) {
      header('Location: ./');
      exit;
    }

    include 'views/login.php';
  }

  /**
   * Procesa el formulario de login.
   * Intenta autenticar al usuario con las credenciales proporcionadas.
   * Si las credenciales son correctas, redirige a la página principal.
   * Si son incorrectas, muestra el formulario de login con un mensaje de error.
   *
   * @return void
   */
  public function doLogin() {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (Auth::attempt($email, $password)) {
      header('Location: ./');
    } else {
      $error = "Credenciales incorrectas o usuario inactivo.";
      include 'views/login.php';
    }
  }

  /**
   * Cierra la sesión del usuario actual y redirige al formulario de login.
   *
   * @return void
   */
  public function logout() {
    Auth::logout();
    header('Location: ./login');
    exit;
  }

  /**
   * Redirige al usuario autenticado según su rol.
   * Si el usuario no está autenticado, lo redirige a la página de login.
   *
   * @return void
   */
  public function redirect() {
    if (Auth::check()) {
      switch (Auth::getRole()) {
        case 'admin':
          header('Location: ./admin');
          break;

        case 'profesor':
          header('Location: ./profesor');
          break;

        default:
          header('Location: ./alumno');
          break;
      }
    } else {
      header('Location: ./login');
    }
    exit;
  }
}
