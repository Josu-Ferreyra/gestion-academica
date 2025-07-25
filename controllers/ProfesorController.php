<?php

require_once 'core/Auth.php';
require_once 'models/Profesor.php';
require_once 'models/Usuario.php';
require_once 'models/Materia.php';
require_once 'models/Carrera.php';

class ProfesorController {
  /**
   * Muestra la vista principal para los profesores.
   * Verifica que el usuario autenticado tenga el rol de "profesor".
   * Si no es un profesor o no está autenticado, devuelve un código de respuesta 403.
   *
   * @return void
   */
  public function index() {
    $user = Auth::user();

    if (!$user || $user['rol'] !== 'profesor') {
      http_response_code(403);
      echo "403 - Acceso no autorizado";
      return;
    }

    include 'views/profesor/index.php';
  }

  /**
   * Muestra la vista para crear un nuevo profesor.
   * Obtiene las carreras y materias disponibles desde el modelo.
   *
   * @return void
   */
  public function viewCreateProfesor() {
    $carreras = Carrera::getAllCarreras();
    $materias = Materia::getAllMaterias();

    include 'views/profesor/create.php';
  }

  /**
   * Crea un nuevo profesor.
   * Valida los datos recibidos del formulario y crea un usuario y un profesor en la base de datos.
   * Si hay algún error, devuelve un código de respuesta 500.
   *
   * @return void
   */
  public function createProfesor() {
    try {
      $id_usuario = Usuario::createUsuario([
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'contrasena' => $_POST['contrasena'],
        'email' => $_POST['email'],
        'id_rol' => $_POST['id_rol'],
        'direccion' => $_POST['direccion'],
        'telefono' => $_POST['telefono'],
        'activo' => $_POST['activo']
      ]);

      $id_profesor = Profesor::createProfesor([
        'id_usuario' => $id_usuario,
        'titulo_academico' => $_POST['titulo_academico'],
        'especialidad' => $_POST['especialidad'],
        'materias' => isset($_POST['materias']) ? $_POST['materias'] : []
      ]);

      echo "Profesor creado exitosamente con ID: " . htmlspecialchars($id_profesor);
    } catch (Exception $e) {
      http_response_code(500);
      echo "Error al crear el profesor: " . $e->getMessage();
      return;
    }
  }
}
