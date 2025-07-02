<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión Académica - Alumno</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <h1>Materias de la Carrera</h1>
  <table border="1">
    <thead>
      <tr>
        <th>ID Materia</th>
        <th>Nombre</th>
        <th>Año</th>
        <th>Semestre</th>
        <th>Parcial 1</th>
        <th>Parcial 2</th>
        <th>Recuperatorio 1</th>
        <th>Recuperatorio 2</th>
        <th>Nota Final</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($materiasAlumno as $key => $value): ?>
        <tr id="materia-<?php echo $key; ?>">
          <td><?php echo $key; ?></td>
          <td><?php echo $value['nombre']; ?></td>
          <td><?php echo $value['anio']; ?></td>
          <td><?php echo $value['semestre']; ?></td>
          <?php if (empty($value['inscripciones'])): ?>
            <td colspan="5">No inscrito</td>
            <td>No inscrito</td>
          <?php else: ?>
            <?php
            $inscripcion = $value['inscripciones'][0]; // Asumiendo una inscripción por materia
            $notas = [
              'parcial_1' => $inscripcion['parcial_1'] ?? 'Sin nota',
              'parcial_2' => $inscripcion['parcial_2'] ?? 'Sin nota',
              'recuperatorio_1' => $inscripcion['recuperatorio_1'] ?? 'Sin nota',
              'recuperatorio_2' => $inscripcion['recuperatorio_2'] ?? 'Sin nota',
              'nota_final' => $inscripcion['nota_final'] ?? 'Sin nota'
            ];
            ?>
            <td><?php echo $notas['parcial_1']; ?></td>
            <td><?php echo $notas['parcial_2']; ?></td>
            <td><?php echo $notas['recuperatorio_1']; ?></td>
            <td><?php echo $notas['recuperatorio_2']; ?></td>
            <td><?php echo $notas['nota_final']; ?></td>
            <td><?php echo $inscripcion['estado_inscripcion']; ?></td>
          <?php endif; ?>
          <td>
            <?php if (empty($value['inscripciones'])): ?>
              <button class="inscribir-btn" data-id="<?php echo $key; ?>">Inscribirme</button>
            <?php else: ?>
              Inscrito
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <script>
    $(document).ready(function() {
      $('.inscribir-btn').on('click', function() {
        const materiaId = $(this).data('id');
        $.ajax({
          url: '/gestion-academica/?url=alumno/inscribirMateria',
          type: 'POST',
          data: {
            materia_id: materiaId
          },
          success: function(response) {
            if (response.success) {
              alert(response.mensaje);

              const row = $(`#materia-${materiaId}`);
              console.log(`ROW: ${JSON.stringify(row)}`);
              row.find('td:nth-child(5)').text('Sin nota');
              row.find('td:nth-child(6)').text('cursando');
              row.find('td:nth-child(7)').text('Inscrito');
              row.find('.inscribir-btn').remove();
            } else {
              alert(response.mensaje);
            }
          },
          error: function() {
            alert('Error al intentar inscribirse en la materia');
          }
        });
      });
    });
  </script>
</body>

</html>
