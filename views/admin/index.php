<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Panel de Administración</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f6f6f6;
      padding: 2rem;
    }

    .panel {
      background: #fff;
      max-width: 700px;
      margin: 2rem auto;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
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
    <h1>Panel de Administración</h1>
    <p>Hola, <?= htmlspecialchars($user['nombre']) ?>. Estás accediendo como <strong>Administrador</strong>.</p>
    <p>Desde aquí podrás gestionar usuarios, carreras, materias y otros módulos clave del sistema.</p>
  </div>
</body>

</html>
