<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Crear Alumno</title>
</head>

<body>
  <h1>Crear Alumno</h1>

  <form action="./create" method="POST">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id="nombre" required>
    <br>

    <label for="apellido">Apellido:</label>
    <input type="text" name="apellido" id="apellido" required>
    <br>

    <label for="contrasena">Contraseña:</label>
    <input type="password" name="contrasena" id="contrasena" required>
    <br>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <br>

    <label for="direccion">Dirección:</label>
    <input type="text" name="direccion" id="direccion">
    <br>

    <label for="telefono">Teléfono:</label>
    <input type="text" name="telefono" id="telefono">
    <br>

    <label for="activo">Activo:</label>
    <select name="activo" id="activo">
      <option value="1">Sí</option>
      <option value="0">No</option>
    </select>
    <br>

    <input type="hidden" name="id_rol" value="2">

    <label for="id_carrera">Carrera:</label>
    <select name="id_carrera" id="id_carrera" required>
      <?php foreach ($carreras as $carrera): ?>
        <option value="<?= htmlspecialchars($carrera['id_carrera']) ?>"><?= htmlspecialchars($carrera['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
    <br>

    <label for="fecha_ingreso">Fecha de Ingreso:</label>
    <input type="date" name="fecha_ingreso" id="fecha_ingreso" required>
    <br>

    <button type="submit">Crear Alumno</button>
  </form>

  <a href="../admin">Volver al Panel de Administración</a>
</body>

</html>
