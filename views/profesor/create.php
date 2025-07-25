<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Crear Profesor</title>
</head>

<script>
  var materias = <?= json_encode($materias); ?>;
  var selectedMateriasId = [];

  document.addEventListener('DOMContentLoaded', function() {
    renderSelect();
  });

  // Renderiza el select de materias basado en la carrera seleccionada
  function renderSelect() {
    let selectCarrera = document.getElementById('id_carrera');
    let selectMateria = document.getElementById('id_materia');

    let id_carrera = Number(selectCarrera.value);

    let materiasOptions = materias
      .filter(materia => !selectedMateriasId.includes(materia.id_materia) && materia.id_carrera === id_carrera)
      .map(materia => `<option value="${materia.id_materia}">Año: ${materia.anio} | Semestre: ${materia.semestre} | ${materia.nombre}</option>`);

    selectMateria.innerHTML = `${materiasOptions.join('')}`;
  }

  // Agrega una materia seleccionada al array y al DOM
  function addMateria() {
    let select = document.getElementById('id_materia');
    let selectedOption = select.options[select.selectedIndex];

    if (selectedOption) {
      let materiaId = Number(selectedOption.value);

      if (!selectedMateriasId.includes(materiaId)) {
        selectedMateriasId.push(materiaId);
        renderSelect();

        let materiasSeleccionadasDiv = document.getElementById('materias_seleccionadas');
        let newMateriaDiv = document.createElement('div');

        newMateriaDiv.innerHTML = `
          <span>${selectedOption.textContent}</span>
          <button type="button" onclick="removeMateria(${materiaId})">Eliminar</button>
        `;

        materiasSeleccionadasDiv.appendChild(newMateriaDiv);
        addHiddenInput(materiaId);
      } else {
        alert('Materia ya seleccionada.');
      }
    } else {
      alert('Por favor, selecciona una materia.');
    }
  }

  // Elimina una materia del array y del DOM
  function removeMateria(materiaId) {
    selectedMateriasId = selectedMateriasId.filter(id => id !== materiaId);
    removeHiddenInput(materiaId)
    renderSelect();

    let materiasSeleccionadasDiv = document.getElementById('materias_seleccionadas');
    materiasSeleccionadasDiv.innerHTML = '';

    selectedMateriasId.forEach(id => {
      let materia = materias.find(m => m.id_materia === id);
      if (materia) {
        let newMateriaDiv = document.createElement('div');
        newMateriaDiv.innerHTML = `
          <span>Año: ${materia.anio} | Semestre: ${materia.semestre} | ${materia.nombre}</span>
          <button type="button" onclick="removeMateria(${id})">Eliminar</button>
        `;
        materiasSeleccionadasDiv.appendChild(newMateriaDiv);
      }
    });
  }

  // Agrega un input oculto al formulario para enviar las materias seleccionadas
  function addHiddenInput(materiaId) {
    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'materias[]';
    input.value = materiaId;
    document.forms[0].appendChild(input);
  }

  // Elimina el input oculto del formulario
  function removeHiddenInput(materiaId) {
    let inputs = document.querySelectorAll('input[name="materias[]"]');
    inputs.forEach(input => {
      if (input.value == materiaId) {
        input.remove();
      }
    });
  }

  document.forms[0].addEventListener('submit', function(event) {
    if (selectedMateriasId.length === 0) {
      event.preventDefault();
      alert('Por favor, selecciona al menos una materia.');
    }
  });
</script>

<body>
  <h1>Crear Profesor</h1>

  <form action="./create" method="POST">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id="nombre" required>
    <br>

    <label for="apellido">Apellido:</label>
    <input type="text" name="apellido" id="apellido" required>
    <br>

    <label for="contrasena">Contraseña:</label>
    <input type="password" name="contrasena" id="contrasena" required>
    <br>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <br>

    <label for="direccion">Dirección:</label>
    <input type="text" name="direccion" id="direccion">
    <br>

    <label for="telefono">Teléfono:</label>
    <input type="text" name="telefono" id="telefono">
    <br>

    <label for="titulo_academico">Título Académico:</label>
    <input type="text" name="titulo_academico" id="titulo_academico" required>
    <br>

    <label for="especialidad">Especialidad:</label>
    <input type="text" name="especialidad" id="especialidad">
    <br>

    <label for="activo">Activo:</label>
    <select name="activo" id="activo">
      <option value="1">Sí</option>
      <option value="0">No</option>
    </select>
    <br>

    <input type="hidden" name="id_rol" value="3">

    <label for="id_carrera">Carrera: </label>
    <select name="id_carrera" id="id_carrera" onchange="renderSelect()">
      <?php foreach ($carreras as $carrera): ?>
        <option value="<?= $carrera['id_carrera'] ?>"><?= htmlspecialchars($carrera['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
    <br>

    <label for="id_materia">Materias que dicta:</label>
    <select name="id_materia" id="id_materia" required></select>
    <button type="button" onclick="addMateria()">+</button>
    <br>

    <label for="materias[]">Materias Seleccionadas:</label>
    <div id="materias_seleccionadas"></div>

    <label for="fecha_ingreso">Fecha de Ingreso:</label>
    <input type="date" name="fecha_ingreso" id="fecha_ingreso" required>
    <br>

    <button type="submit">Crear Profesor</button>
  </form>

  <a href="../admin">Volver al Panel de Administración</a>
</body>

</html>
