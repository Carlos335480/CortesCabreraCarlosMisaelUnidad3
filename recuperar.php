<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombre = !empty($_SESSION['usuario']) ? $_SESSION['usuario']['name'] : "Invitado";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperar Contraseña</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/estilos_recuperar.css">
</head>
<body>
  <header class="menu">
    <nav>
      <a href=""><i class="fa-solid fa-list"></i> Recuperación Contraseña</a>
    </nav>
  </header>

  <div class="container">
    <h1>Recuperar Contraseña</h1>
    <p>Ingresa tu correo electrónico para recibir un enlace de recuperación.</p>

    <form action="../controllers/procesar_recuperación.php" method="POST">
      <div class="input-icon">
        <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
        <i class="fas fa-envelope"></i>
      </div>
      <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Enviar enlace</button>
    </form>

    <p class="texto-extra">
      <a href="../index.php" class="link-login">Volver al inicio de sesión</a>
    </p>
  </div>
</body>
</html>
