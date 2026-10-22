<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['usuario'])) {
    $nombre = $_SESSION['usuario']['name'];
} else {
    $nombre = "Invitado";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar Ropa</title>
  <link rel="stylesheet" href="../assets/css/estilos_agregar_ropa.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@500&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <header class="menu">
    <div>
      <span><i class="fas fa-user"></i> <?= isset($nombre) ? htmlspecialchars($nombre) : "Invitado" ?></span>
    </div>
    <nav>
      <a href = "../controllers/buzon.php"><i class="fa-solid fa-bell"></i> Buzón</a>
      <a href = "../contactanos.php"><i class="fa-solid fa-phone"></i> Contáctanos</a>
      <a href = "../controllers/obtener_inventario.php"><i class="fa-solid fa-box"></i> Inventario</a>
      <a href = "../mapa.php"><i class="fa-solid fa-map"></i> Mapa</a>
      <a href = "../ayuda.php"><i class="fas fa-concierge-bell"></i> Ayuda</a>
      <a href = "../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </nav>
  </header>

  <div class="container">
    <div class="grupo">
      <h1>Agregar Ropa</h1>
      <form action="controllers/guardar_ropa.php" method="POST" enctype="multipart/form-data">
        <div class="input-icon">
          <input type="text" name="marca" id="marca" placeholder="Marca" required>
          <i class="fas fa-tag"></i>
        </div>
        <div class="input-icon">
          <input type="text" name="modelo" id="modelo" placeholder="Modelo" required>
          <i class="fas fa-tshirt"></i>
        </div>
        <div class="input-icon">
          <input type="text" name="talla" id="talla" placeholder="Talla" required>
          <i class="fas fa-ruler"></i>
        </div>
        <div class="input-icon">
          <input type="number" step="0.01" name="precio" id="precio" placeholder="Precio" required>
          <i class="fas fa-dollar-sign"></i>
        </div>
        <textarea class="textarea-ropa" name="descripcion" id="descripcion" placeholder="Detalles de la prenda"></textarea>
        <input type="file" name="imagen" id="imagen">
        <button type="submit"><i class="fas fa-save"></i> Guardar</button>
      </form>
      <p class="texto-extra">
        <a href="../controllers/obtener_inventario.php" class="link-login">Ir al Inventario</a>
      </p>
    </div>
  </div>
</body>
</html>
