<!-- app/views/auth/login.php -->
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
</head>

<body>
  <h1>Iniciar Sesión</h1>

  <?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" action="?url=auth/login">
    <label>
      Email de Usuario:
      <input type="email" name="email" required>
    </label><br><br>
    <label>
      Contraseña:
      <input type="password" name="password" required>
    </label><br><br>
    <button type="submit">Entrar</button>
  </form>
</body>

</html>
