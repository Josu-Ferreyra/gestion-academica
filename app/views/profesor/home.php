<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión Académica - Profesor</title>
</head>

<body>
  <header>
    <h1>Bienvenido, Profesor</h1>
  </header>
  <main>
    <section>
      <p><strong>ID de Profesor:</strong> <?php echo htmlspecialchars($profesorId); ?></p>
      <p><strong>Materias Asignadas:</strong> <?php echo count($profesorMaterias); ?></p>
    </section>
    <section>
      <?php if (empty($profesorMaterias)): ?>
        <p>No tienes materias asignadas.</p>
      <?php else: ?>
        <h2>Lista de Materias</h2>
        <ul>
          <?php foreach ($profesorMaterias as $materia): ?>
            <li>
              <a href="/gestion-academica/?url=materia/detalleMateria/<?php echo htmlspecialchars($materia['id_materia']); ?>">
                <?php echo htmlspecialchars($materia['nombre_materia']); ?> (ID: <?php echo htmlspecialchars($materia['id_materia']); ?>)
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>
  </main>
</body>

</html>
