<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Profesor | Detalle de la Materia</title>
</head>

<body>
  <h1>Detalle de la Materia</h1>
  <a href="../materias"> Volver a materias</a>

  <table border="1">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Nota 1er Parcial</th>
        <th>Nota 2do Parcial</th>
        <th>Recuperatorio 1er Parcial</th>
        <th>Recuperatorio 2do Parcial</th>
        <th>Nota Final</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($inscripciones as $inscripcion): ?>
        <tr>
          <td><?= htmlspecialchars($inscripcion['nombre_alumno']) ?></td>
          <td><?= htmlspecialchars($inscripcion['apellido_alumno']) ?></td>
          <td><?= $inscripcion['parcial_1'] ?? "-" ?></td>
          <td><?= $inscripcion['parcial_2'] ?? "-" ?></td>
          <td><?= $inscripcion['recuperatorio_1'] ?? "-" ?></td>
          <td><?= $inscripcion['recuperatorio_2'] ?? "-" ?></td>
          <td><?= $inscripcion['nota_final'] ?? "-" ?></td>
          <td><?= htmlspecialchars($inscripcion['estado_inscripcion']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>

</html>
