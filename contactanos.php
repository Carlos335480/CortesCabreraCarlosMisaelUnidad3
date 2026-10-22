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
  <title>Contáctanos</title>
  <link rel="stylesheet" href="../assets/css/estilos_contacto.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <header class="menu">
      <div>
        <span><i class="fas fa-user"></i> <?= isset($nombre) ? htmlspecialchars($nombre) : "Invitado" ?></span>
      </div>
      <nav>
        <a href = "../controllers/buzon.php"><i class="fa-solid fa-bell"></i> Buzón</a>
        <a href = "../controllers/obtener_inventario.php"><i class="fa-solid fa-box"></i> Inventario</a>
        <a href = "../agregar_ropa.php"><i class="fa-solid fa-plus"></i> Agregar Ropa</a>
        <a href = "../mapa.php"><i class="fa-solid fa-map"></i> Mapa</a>
        <a href = "../ayuda.php"><i class="fas fa-concierge-bell"></i> Ayuda</a>
        <a href = "../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
      </nav>
  </header>

  <div class="contact-container">
    <h1>Contáctanos</h1>
    <p>Hola <?= htmlspecialchars($nombre) ?>, envíanos tu mensaje:</p>

    <form action="../controllers/enviar_mensaje.php" method="POST" class="form-contacto">
      <label for="asunto">Asunto:</label>
      <input type="text" name="asunto" id="asunto" placeholder="Ej. Consulta sobre producto" required>

      <label for="mensaje">Mensaje:</label>
      <textarea name="mensaje" id="mensaje" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>

      <button type="submit" class="btn-enviar">Enviar</button>
    </form>
  </div>
</body>
</html>
