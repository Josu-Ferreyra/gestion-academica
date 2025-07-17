<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Panel del Profesor</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f4f8;
      padding: 2rem;
    }

    .panel {
      background: #fff;
      max-width: 600px;
      margin: 2rem auto;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .logout {
      float: right;
      text-decoration: none;
      color: #dc3545;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <div class="panel">
    <a class="logout" href="./logout">Cerrar sesión</a>
    <h1>Bienvenido, <?= htmlspecialchars($user['nombre']) ?></h1>
    <p>Estás accediendo como <strong>Profesor</strong>.</p>
    <p>Aquí podrás administrar materias, cargar notas y más.</p>
  </div>
</body>

</html>
