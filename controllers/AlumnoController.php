<?php
require_once 'core/Auth.php';
require_once 'models/Usuario.php';
require_once 'models/Alumno.php';
require_once 'models/Carrera.php';

class AlumnoController {
  /**
   * Muestra la vista principal para los alumnos.
   * Verifica que el usuario autenticado tenga el rol de "alumno".
   * Si no es un alumno o no está autenticado, devuelve un código de respuesta 403.
   *
   * @return void
   */
  public function index() {
    $user = Auth::user();

    if (!$user || $user['rol'] !== 'alumno') {
      http_response_code(403);
      echo "403 - Acceso no autorizado";
      return;
    }

    include 'views/alumno/index.php';
  }

  /**
   * Muestra la vista para crear a los alumnos desde el panel de administración.
   * Verifica que el usuario autenticado tenga el rol de "admin".
   * Si no es admin o no está autenticado, devuelve un código de respuesta 403.
   *
   * @return void
   */
  public function viewCreateAlumno() {
    if (!Auth::checkRoles(['admin'])) {
      http_response_code(403);
      echo "403 - Acceso no autorizado";
      return;
    }

    // Obtenemos todas las carreras para mostrarlas en el formulario
    $carreras = Carrera::getAllCarreras();

    include 'views/alumno/create.php';
  }

  /**
   * Crea un nuevo alumno en la base de datos.
   * Verifica que el usuario autenticado tenga el rol de "admin".
   * Si no es admin o no está autenticado, devuelve un código de respuesta 403.
   *
   * @return void
   */
  public function createAlumno() {
    if (!Auth::checkRoles(['admin'])) {
      http_response_code(403);
      echo "403 - Acceso no autorizado";
      return;
    }

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

      $id_alumno = Alumno::createAlumno([
        'id_carrera' => $_POST['id_carrera'],
        'id_usuario' => $id_usuario,
        'fecha_ingreso' => $_POST['fecha_ingreso']
      ]);

      // TODO: Mostrar la vista de éxito
      echo "Alumno creado exitosamente con ID: " . htmlspecialchars($id_alumno);
    } catch (Exception $e) {
      // TODO: Mostrar la vista de error
      http_response_code(400);
      echo "Error al crear el alumno: " . htmlspecialchars($e->getMessage());
      return;
    }
  }
}
