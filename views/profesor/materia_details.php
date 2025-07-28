<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Profesor | Detalle de la Materia</title>
</head>

<script>
  const inscripciones = <?= json_encode($inscripciones) ?>;

  function filterByYear(year, table) {
    const filteredInscripciones = inscripciones.filter(inscripcion => inscripcion.anio_academico === year);

    table.innerHTML = "";

    filteredInscripciones.forEach(inscripcion => {
      table.innerHTML += buildTableRow(inscripcion);
    });
  }

  document.addEventListener("DOMContentLoaded", function() {
    const yearSelect = document.getElementById("yearSelect");
    const editButton = document.getElementById("editButton");
    const saveButton = document.getElementById("saveButton");
    const inscripcionesTable = document.getElementById("inscripcionesTable");

    filterByYear(yearSelect.value, inscripcionesTable);

    // Cambiar el año seleccionado
    yearSelect.addEventListener("change", function() {
      const selectedYear = this.value;
      filterByYear(selectedYear, inscripcionesTable);
    });

    // Mostrar/ocultar botones de edición
    editButton.addEventListener("click", function() {
      inscripcionesTable.querySelectorAll('tbody tr').forEach(row => {
        row.querySelectorAll('td').forEach((cell, index) => {
          if (index >= 3 && index <= 7) { // Only grade columns
            const value = cell.textContent.trim();
            cell.innerHTML = `<input type="number" min="1" max="10" value="${value !== 'N/A' ? value : ''}" />`;
          }
        });
      });

      saveButton.style.display = "inline";
      editButton.style.display = "none";
    });

    saveButton.addEventListener("click", async function() {
      try {
        const message = await editarNotas(inscripcionesTable)
        alert(message)
        location.reload();
      } catch (error) {
        alert("Error al guardar los cambios: " + error.message);
        return;
      }

      saveButton.style.display = "none";
      editButton.style.display = "inline";
    });
  });

  function buildTableRow(inscripcion) {
    return `
      <tr>
        <td>${inscripcion.id_alumno}</td>
        <td>${inscripcion.nombre_alumno}</td>
        <td>${inscripcion.apellido_alumno}</td>
        <td>${inscripcion.parcial_1 ?? "-"}</td>
        <td>${inscripcion.parcial_2 ?? "-"}</td>
        <td>${inscripcion.recuperatorio_1 ?? "-"}</td>
        <td>${inscripcion.recuperatorio_2 ?? "-"}</td>
        <td>${inscripcion.nota_final ?? "-"}</td>
        <td>${inscripcion.estado_inscripcion}</td>
      </tr>
    `;
  }

  async function editarNotas(table) {
    const rows = table.querySelectorAll('tbody tr');
    const alumnos = [];

    rows.forEach(row => {
      const idAlumno = row.querySelector('td:nth-child(1)').textContent.trim();
      const inputs = row.querySelectorAll('input');
      if (inputs.length > 0) {
        alumnos.push({
          id_alumno: idAlumno,
          parcial_1: inputs[0].value || null,
          parcial_2: inputs[1].value || null,
          recuperatorio_1: inputs[2].value || null,
          recuperatorio_2: inputs[3].value || null,
          nota_final: inputs[4].value || null,
        });
      }
    });

    const res = await fetch(`../../alumno/update_notas`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        alumnos,
        id_materia: <?= $id_materia ?>,
        year: yearSelect.value,
      }),
    })

    if (res.status !== 200) {
      throw new Error("Error al actualizar las notas");
    }

    const data = await res.json();

    return data.message
  }
</script>

<body>
  <h1>Detalle de la Materia</h1>
  <a href="../materias"> Volver a materias</a>
  <div>
    <label for="yearSelect">Año:</label>
    <select id="yearSelect">
      <option value="2024">2024</option>
      <option value="2025" selected>2025</option>
    </select>
  </div>

  <div>
    <button id="editButton">Editar notas</button>
    <button id="saveButton" style="display: none;">Guardar</button>
  </div>

  <table border="1">
    <thead>
      <tr>
        <th>ID Alumno</th>
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
    <tbody id="inscripcionesTable">
    </tbody>
  </table>

</body>

</html>
