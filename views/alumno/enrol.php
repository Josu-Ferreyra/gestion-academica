<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Alumno | Inscripción a Materia</title>
</head>

<script>
  async function enrol(idMateria) {
    if (!confirm("¿Estás seguro de que quieres inscribirte a esta materia?")) {
      return;
    }

    try {
      const res = await fetch("../alumno/enrol", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id_materia: idMateria
        })
      })

      if (res.status !== 200) {
        const error = await res.json();
        throw new Error(error.message || "Error al inscribirse a la materia");
      }

      removeRow(idMateria)
      alert("Inscripción exitosa");
    } catch (e) {
      alert("Ocurrió un error al intentar inscribirte. Por favor, inténtalo de nuevo más tarde.");
      return;
    }
  }

  function removeRow(idMateria) {
    const row = document.getElementById(idMateria);
    if (row) {
      row.remove();
    } else {
      console.error("No se encontró la fila con el ID:", idMateria);
    }
  }
</script>

<body>
  <h1>Materias disponibles</h1>
  <a href="../"> Volver al panel del alumno</a>

  <table border="1">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Año</th>
        <th>Semestre</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($materias_disponibles as $materia): ?>
        <tr id="<?= $materia['id_materia'] ?>">
          <td><?= htmlspecialchars($materia['nombre']) ?></td>
          <td><?= htmlspecialchars($materia['anio']) ?></td>
          <td><?= htmlspecialchars($materia['semestre']) ?></td>
          <td>
            <button id="enrol" onclick="enrol(<?= $materia['id_materia'] ?>)">Inscribirme</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>

</html>
