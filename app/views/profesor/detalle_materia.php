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

  <div id="tablaAlumnos"></div>
</body>

</html>
