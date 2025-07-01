<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión Académica - Alumno</title>
</head>

<body>
  <?php
  echo "<p>Materias: " . count($materiasAlumno) . "</p>";
  echo "<p>Detalles Materias: </p>";
  foreach ($materiasAlumno as $key => $value) {
    echo "<p>Materia ID: $key</p>";
    echo "<p>Nombre: " . $value['nombre'] . "</p>";
    echo "<p>Año: " . $value['anio'] . "</p>";
    echo "<p>Semestre: " . $value['semestre'] . "</p>";
    echo "<p>Inscripciones:</p>";
    if (empty($value['inscripciones'])) {
      echo "<p>No hay inscripciones para esta materia.</p>";
      echo "<button onclick=\"location.href='/gestion-academica/alumno/inscripcionMateria/$key'\">Inscribir Materia</button>";
    } else {
      foreach ($value['inscripciones'] as $inscripcion) {
        echo "<p>Año Académico: " . $inscripcion['anio_academico'] . "</p>";
        echo "<p>Semestre: " . $inscripcion['semestre'] . "</p>";
        echo "<p>Intentos Final: " . $inscripcion['intentos_final'] . "</p>";
        echo "<p>Nombre Materia: " . $inscripcion['nombre_materia'] . "</p>";
        echo "<p>Estado Inscripción: " . $inscripcion['estado_inscripcion'] . "</p>";
        if (!empty($inscripcion['fecha_evaluacion'])) {
          echo "<p>Fecha Evaluación: " . $inscripcion['fecha_evaluacion'] . "</p>";
          echo "<p>Nota Evaluación: " . $inscripcion['nota_evaluacion'] . "</p>";
        } else {
          echo "<p>No hay evaluaciones registradas.</p>";
        }
      }
    }
    echo "<hr>";
  }
  ?>
</body>

</html>
