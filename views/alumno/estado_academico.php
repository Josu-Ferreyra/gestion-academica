<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Alumno | Estado Académico</title>
</head>

<body>
  <h1>Estado Académico</h1>
  <a href="../"> Volver al panel del alumno</a>

  <table border="1">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Año</th>
        <th>Semestre</th>
        <th>Año de Cursado</th>
        <th>Nota 1er Parcial</th>
        <th>Nota 2do Parcial</th>
        <th>Recuperatorio 1er Parcial</th>
        <th>Recuperatorio 2do Parcial</th>
        <th>Nota Final</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($materias as $materia): ?>
        <tr>
          <td><?= htmlspecialchars($materia['nombre_materia']) ?></td>
          <td><?= htmlspecialchars($materia['anio']) ?></td>
          <td><?= htmlspecialchars($materia['semestre']) ?></td>
          <td><?= htmlspecialchars($materia['anio_academico']) ?></td>
          <td><?= $materia['parcial_1'] ?? "-" ?></td>
          <td><?= $materia['parcial_2'] ?? "-" ?></td>
          <td><?= $materia['recuperatorio_1'] ?? "-" ?></td>
          <td><?= $materia['recuperatorio_2'] ?? "-" ?></td>
          <td><?= $materia['nota_final'] ?? "-" ?></td>
          <td><?= htmlspecialchars($materia['estado_inscripcion']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>

</html>
