<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión Académica - Profesor</title>
</head>

<body>
  <?php
  echo "<h1>Bienvenido, Profesor</h1>";
  echo "<p>ID de Profesor: " . htmlspecialchars($profesorId) . "</p>";
  echo "<p>Materias Asignadas: " . count($profesorMaterias) . "</p>";
  if (empty($profesorMaterias)) {
    echo "<p>No tienes materias asignadas.</p>";
  } else {
    echo "<ul>";
    foreach ($profesorMaterias as $materia) {
      echo "<li>" . htmlspecialchars($materia['nombre_materia']) . " (ID: " . htmlspecialchars($materia['id_materia']) . ")</li>";
    }
    echo "</ul>";
  }
  ?>
</body>

</html>
