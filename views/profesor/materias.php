<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Profesor | Materias</title>
</head>

<body>
  <h1>Listado de Materias</h1>
  <a href="../profesor">Volver al Panel de Profesor</a>

  <ul>
    <?php foreach ($materias as $materia): ?>
      <li>
        <strong><?= htmlspecialchars($materia['nombre']) ?></strong> - Año: <?= htmlspecialchars($materia['anio']) ?> | Semestre: <?= htmlspecialchars($materia['semestre']) ?>
        <a href="./materia/details?id=<?= $materia['id_materia'] ?>">Ver detalles</a>
      </li>
    <?php endforeach; ?>
  </ul>

</body>

</html>
