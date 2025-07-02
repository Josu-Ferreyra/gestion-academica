<table border="1">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Apellido</th>
      <th>Parcial 1</th>
      <th>Parcial 2</th>
      <th>Recuperatorio Parcial 1</th>
      <th>Recuperatorio Parcial 2</th>
      <th>Final</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($alumnos)): ?>
      <?php foreach ($alumnos as $alumno): ?>
        <tr>
          <td><?php echo htmlspecialchars($alumno['id_alumno']); ?></td>
          <td><?php echo htmlspecialchars($alumno['nombre_alumno']); ?></td>
          <td><?php echo htmlspecialchars($alumno['apellido_alumno']); ?></td>
          <td><?php echo htmlspecialchars($alumno['parcial_1'] ?? 'N/A'); ?></td>
          <td><?php echo htmlspecialchars($alumno['parcial_2'] ?? 'N/A'); ?></td>
          <td><?php echo htmlspecialchars($alumno['recuperatorio_1'] ?? 'N/A'); ?></td>
          <td><?php echo htmlspecialchars($alumno['recuperatorio_2'] ?? 'N/A'); ?></td>
          <td><?php echo htmlspecialchars($alumno['nota_final'] ?? 'N/A'); ?></td>
          <td><?php echo htmlspecialchars($alumno['estado_inscripcion']); ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="9">No hay alumnos inscriptos en esta materia.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
