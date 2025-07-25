<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Crear Carrera</title>
</head>

<body>
  <h1>Crear Carrera</h1>

  <form action="./create" method="POST">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id="nombre" required>
    <br>

    <button type="submit">Crear Carrera</button>
  </form>

  <a href="../admin">Volver al Panel de Administración</a>
</body>

</html>
