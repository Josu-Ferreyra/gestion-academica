<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Crear Materia</title>
</head>

<body>
  <h1>Crear Materia</h1>

  <form action="./create" method="POST">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id="nombre" required>
    <br>

    <label for="anio">Año:</label>
    <input type="number" name="anio" id="anio" min="1" max="5" required>
    <br>

    <label for="semestre">Semestre:</label>
    <input type="number" name="semestre" id="semestre" min="1" max="2" required>
    <br>

    <label for="id_carrera">Carrera:</label>
    <select name="id_carrera" id="id_carrera" required>
      <?php foreach ($carreras as $carrera): ?>
        <option value="<?= htmlspecialchars($carrera['id_carrera']) ?>">
          <?= htmlspecialchars($carrera['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <br>

    <label for="id_profesor">Profesor:</label>
    <select name="id_profesor" id="id_profesor">
      <option value="-1" disabled selected hidden>-- Seleccionar Profesor --</option>
      <option value="">Ninguno</option>
      <?php foreach ($profesores as $profesor): ?>
        <option value="<?= htmlspecialchars($profesor['id_profesor']) ?>">
          <?= htmlspecialchars($profesor['nombre']) . ' ' . htmlspecialchars($profesor['apellido']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <br>

    <button type="submit">Crear Materia</button>
  </form>

  <a href="../admin">Volver al Panel de Administración</a>
</body>

</html>
