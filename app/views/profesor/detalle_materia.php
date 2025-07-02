<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Materia</title>
  <script>
    function loadTablaAlumnos(materiaId, year) {
      const tablaAlumnos = document.querySelector('#tablaAlumnos');

      fetch(`./?url=materia/getAllMateriaAlumnosByYear/${materiaId}/${year}`)
        .then(response => response.text())
        .then(data => {
          tablaAlumnos.innerHTML = data;
        });
    }

    function enableEditing() {
      const tablaAlumnos = document.querySelector('#tablaAlumnos');
      const editButton = document.getElementById('editButton');
      const saveButton = document.getElementById('saveButton');

      // Replace table cells with input fields
      tablaAlumnos.querySelectorAll('tbody tr').forEach(row => {
        row.querySelectorAll('td').forEach((cell, index) => {
          if (index >= 3 && index <= 7) { // Only grade columns
            const value = cell.textContent.trim();
            cell.innerHTML = `<input type="number" min="1" max="10" value="${value !== 'N/A' ? value : ''}" />`;
          }
        });
      });

      // Hide "Editar notas" button and show "Guardar" button
      editButton.style.display = 'none';
      saveButton.style.display = 'inline-block';
    }

    function saveNotas(materiaId, year) {
      const tablaAlumnos = document.querySelector('#tablaAlumnos');
      const rows = tablaAlumnos.querySelectorAll('tbody tr');
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

      fetch(`./?url=materia/updateNotas/${materiaId}/${year}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            alumnos
          }),
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Notas actualizadas correctamente.');
            location.reload();
          } else {
            alert('Error al actualizar las notas.');
          }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
      const yearSelect = document.getElementById('yearSelect');
      const editButton = document.getElementById('editButton');
      const saveButton = document.getElementById('saveButton');
      const tablaAlumnos = document.querySelector('#tablaAlumnos');
      const materiaId = new URLSearchParams(window.location.search).get('url').split('/')[2];

      // Load initial data for the current year
      loadTablaAlumnos(materiaId, yearSelect.value);

      // Handle year change
      yearSelect.addEventListener('change', () => {
        const selectedYear = yearSelect.value;
        loadTablaAlumnos(materiaId, selectedYear);
      });

      // Enable editing
      editButton.addEventListener('click', enableEditing);

      // Save notes
      saveButton.addEventListener('click', () => {
        const selectedYear = yearSelect.value;
        saveNotas(materiaId, selectedYear);
      });
    });
  </script>
</head>

<body>
  <h1>Alumnos Inscriptos en la Materia</h1>
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

  <div id="tablaAlumnos"></div>
</body>

</html>
